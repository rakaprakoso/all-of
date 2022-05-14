<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BuyerShipping;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EcommerceController extends Controller
{

    public function category(){
        $responseMessage = '';
        try {
            $category = Category::get();

            $responseMessage = 'Product Get Successfully!';
            return $this->responseSuccess($responseMessage, $category);
        } catch (\Throwable $th) {
            $responseMessage = 'Product Error!';
            return $this->responseFail($responseMessage, null);
        }
    }

    public function getCart(){
        $responseMessage = '';
        $selectColumns = ['id','name','price','discount_price','slug','thumbnail_img'];
        $data = auth()->user();
        if ($data) {
            try {
                $cartData = Cart::where('user_id',$data->id)->first();
                // $productID = $cartData->cart_detail->pluck('product_id');
                $cartList = $cartData->cart_detail;

                $total = $cartData->getTotalQty();
                $totalPrice = $cartData->getTotalPrice();
                // $product = Product::whereIn('id',$productID)->select($selectColumns)->get();

                $cart = [];
                foreach ($cartList as $key => $value) {
                    $cart[] = array_merge($value->toArray(),$value->product->only($selectColumns));
                }

                $responseMessage = 'Cart Get!';
                return $this->responseSuccess($responseMessage, ["cart"=>$cart,"total"=>$total,"total_price"=>$totalPrice]);
            } catch (\Throwable $th) {
                $responseMessage = 'Cart Error!';
                return $this->responseFail($responseMessage, null);
            }
        } else {
            $responseMessage = 'Cart Zero!';
            return $this->responseSuccess($responseMessage, null);
        }

    }

    public function postCart(Request $request){
        $responseMessage = '';
        $selectColumns = ['id','name','price','discount_price','slug','thumbnail_img'];
        $auth = auth()->user();
        if ($auth) {
            try {
                $cartID = Cart::where('user_id',$auth->id)->first()->id;
                // $newCart = CartDetail::where('cart_id',$cartID)->where('product_id',$request->id)->update(['qty'=>$request->amount]);
                $qty = $request->amount;

                if ($request->add) {
                    $oldCart = CartDetail::where('cart_id',$cartID)->where('product_id',$request->id)->first();
                    if ($oldCart) {
                        $qty = $oldCart->qty + $request->amount;
                    }
                }

                $newCart = CartDetail::updateOrCreate(
                    ['cart_id' =>  $cartID,'product_id'=>$request->id],
                    ['qty' => $qty]
                );


                return $this->getCart();
                // return $this->responseSuccess($responseMessage, ["cart"=>$cart,"total"=>$total]);
            } catch (\Throwable $th) {
                $responseMessage = 'Cart Error!';
                return $this->responseFail($responseMessage, null);
            }
        } else {
            $responseMessage = 'Not Authenticated';
            return $this->responseSuccess($responseMessage, null);
        }

    }

    public function getCheckoutAddress()
    {
        $responseMessage = '';
        $isAuth = auth()->user();
        if ($isAuth) {
            try {
                $shippingData = BuyerShipping::where('user_id',$isAuth->id)->where('main',true)->first();

                $responseMessage = 'Data Get!';
                return $this->responseSuccess($responseMessage, $shippingData);
            } catch (\Throwable $th) {
                $responseMessage = 'Data Error!';
                return $this->responseFail($responseMessage, null);
            }
        } else {
            $responseMessage = 'Data Zero!';
            return $this->responseSuccess($responseMessage, null);
        }
    }
    public function indexOrder()
    {
        $responseMessage = '';
        $isAuth = auth()->user();
        if ($isAuth) {
            try {
                $order = Order::where('user_id',$isAuth->id)->get();

                $responseMessage = 'Data Get!';
                return $this->responseSuccess($responseMessage, $order);
            } catch (\Throwable $th) {
                $responseMessage = 'Data Error!';
                return $this->responseFail($responseMessage, null);
            }
        } else {
            $responseMessage = 'Data Zero!';
            return $this->responseSuccess($responseMessage, null);
        }
    }

    public function showOrder($id)
    {
        $responseMessage = '';
        $isAuth = auth()->user();
        if ($isAuth) {
            try {
                $order = Order::where('order_id',$id)->where('user_id',$isAuth->id)->first()->toArray();
                if ($order !== null) {
                    $order['normal_price'] = 0;
                    $order['discount_price'] = 0;
                    $order['net_price'] = 0;
                    foreach ($order['order_details'] as $key => $value) {
                        $order['normal_price'] += $value['price'] * $value['qty'];
                        $order['net_price'] += $value['price'] * $value['qty'];
                    }

                    $typelink = $this->isProduction() ? "" : "sandbox";
                    $order['link'] = "https://app." . $typelink . ".midtrans.com/snap/v2/vtweb/" . $order['payment']['midtrans_order_id'];
                    try {
                        $data['status'] = \Midtrans\Transaction::status($id);
                    } catch (\Exception $e) {
                        $data['status'] = null;
                    }
                }

                $responseMessage = 'Data Get!';
                return $this->responseSuccess($responseMessage, $order);
            } catch (\Throwable $th) {
                $responseMessage = 'Data Error!';
                return $this->responseFail($responseMessage, null);
            }
        } else {
            $responseMessage = 'Data Zero!';
            return $this->responseSuccess($responseMessage, null);
        }
    }

    public function placeOrder(Request $request){
        $responseMessage = '';
        $auth = auth()->user();
        if ($auth) {
            try {
                $order = $this->createOrder($request);
                // return $this->responseSuccess($responseMessage, $request->all());


                // return $this->getCart();
                return $this->responseSuccess($responseMessage, $order);
            } catch (\Throwable $th) {
                $responseMessage = 'Cart Error!';
                return $this->responseFail($responseMessage, null);
            }
        } else {
            $responseMessage = 'Not Authenticated';
            return $this->responseSuccess($responseMessage, null);
        }
    }

    private function createOrder($request){
        $data['price']['normal_price'] = 0;
        $data['price']['discount_price'] = 0;
        $data['price']['coupon'] = 0;
        $data['price']['net_price'] = 0;
        $data['total_qty'] = 0;
        $data['weight'] = 0;
        $items = [];
        /*$data['cart'] = Cart::where('user_id',Auth::guard('user')->user()->id)->first();
        $data['cart'] = $data['cart']!=null ? $data['cart']->cart_detail : null;
        if ($data['cart']!=null) {
            foreach ($data['cart'] as $key => $value) {
                if ($value->selected==1) {
                    $data['price']['normal_price']+=$value->product->price*$value->qty;
                    //$data['price']['discount_price']+=$value->product->price*$value->qty;
                    $data['price']['net_price']+=$value->product->price*$value->qty;
                }
            }
        }
        return $data['cart'];*/
        $discount_amount = 0;


        //SIMPAN DATA ORDER
        $order = new Order();
        // $order->user_id=Auth::guard('user')->user()->id;
        $order->order_id = 'TRA-' . time();
        $order->user_id = auth()->user()->id;
        $order->order_date = Carbon::now()->toDateTimeString();

        $order->name_buyer = $request->first_name.' '.$request->last_name;
        $order->phone_buyer = $request->phone;
        $order->email_buyer = $request->email;
        $order->address_buyer = $request->address;
        $order->shipping_method = $request->shipping_method;

        $order->save();

        //LOOPING DATA DI CART
        foreach ($request->product_id as $key => $product_id) {
            //AMBIL DATA PRODUK BERDASARKAN PRODUCT_ID
            $product = Product::find($product_id);
            //SIMPAN DETAIL ORDER
            $orderDetail = new OrderDetail;
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $product->id;
            // $orderDetail->price=$this->markupPrice($product->price);
            $orderDetail->price = $product->discount_price ? $product->discount_price : $product->price;
            $orderDetail->qty = $request->qty[$key];
            $orderDetail->save();
            //$orderDetail->sales_code=$request->sales_code;

            $data['total_qty'] += $request->qty[$key];
            // $data['price']['normal_price']+=$this->markupPrice($product->price)*$request->qty[$key];
            $data['price']['normal_price'] += $product->price * $request->qty[$key];
            $data['price']['discount_price'] += empty($product->discount_price) ? 0 : ($product->price - $product->discount_price) * $orderDetail->qty;
            // $data['total_qty']*$this->shop_config['markup_price'];
            // $data['price']['net_price']= $data['price']['normal_price']-$data['price']['discount_price'];
            $data['weight'] += $product->weight * $request->qty[$key];

            $itemBarang = array(
                'id'                => $product_id,
                'price'         => $orderDetail->price,
                'quantity'  => $orderDetail->qty,
                'name'          => $product->name
            );
            array_push($items, $itemBarang);
        }
        $data['price']['net_price'] += $data['price']['normal_price'] - $data['price']['discount_price'];

        // $shippingData = $this->printShipping($request->city_id, $data['weight'], $request->shipping_method);
        // return $shippingData;

        // $order->shippingAddressBuyer = $shippingData['address'];
        // $order->shipping_cost = $shippingData['cost'] ?? 0;

        $order->shipping_address_buyer = $request->address;
        $order->shipping_cost = 0;
        $itemShippingCost = array(
            'id'                => 'shipcost',
            'price'         => $order->shipping_cost,
            'quantity'  => 1,
            'name'          => 'Shipping Cost'
        );
        array_push($items, $itemShippingCost);

        if ($request->couponcode) {
            $data['price']['coupon'] = $this->couponCalculate($request->couponcode);
            if ($data['price']['coupon']['amount'] > 0) {
                $order->couponcode = $request->couponcode;
                if ($data['price']['coupon']['type'] == 'percent') {
                    $order->couponamount = ($data['price']['coupon']['amount'] / 100) *  $data['price']['net_price'];
                } else {
                    $order->couponamount = $data['price']['coupon']['amount'];
                }
                $data['price']['net_price'] -= $data['price']['coupon']['amount'];
                $itemDiscount = array(
                    'id'                => 'disc',
                    'price'         => -$order->couponamount,
                    'quantity'  => 1,
                    'name'          => 'Discount Code : ' . $request->couponcode,
                );
                array_push($items, $itemDiscount);
            }
        }

        $order->save();

        // HAPUS CART DB
        $removeCart= Cart::where('user_id',auth()->user()->id)->first();
        CartDetail::where('cart_id',$removeCart->id)
        ->whereIn('product_id',$request->product_id)->delete();

        $serverKeyStag = 'SB-Mid-server-dlH7tIoiMKizSzMfEDZ0kwRU';
        $serverKeyProd = 'Mid-server-arg9_GzmzUqX5peJURM0cCce';
        // Set sanitization on (default)
        $isSanitized = true;
        // Set 3DS transaction for credit card to true
        $is3ds = true;

        \Midtrans\Config::$isProduction = $this->isProduction();
        \Midtrans\Config::$serverKey = $this->isProduction() ? $serverKeyProd : $serverKeyStag;
        \Midtrans\Config::$isSanitized = $isSanitized;
        \Midtrans\Config::$is3ds = $is3ds;
        $shipping_address = array(
            'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            'address'       => $order->addressBuyer . " - " . $order->shippingAddressBuyer,
        );
        $params = array(
            'transaction_details' => array(
                'order_id' =>  $order->order_id,
                'gross_amount' => $data['price']['net_price'] + $order->shipping_cost,
            ),
            'item_details'           => $items,
            'customer_details' => array(
                // 'first_name' => Auth::guard('user')->user()->name,
                // 'email' => Auth::guard('user')->user()->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'shipping_address' => $shipping_address,
            ),
        );

        try {
            // Get Snap Payment Page URL
            $midtrans_order = \Midtrans\Snap::createTransaction($params);

            $payment = new Payment;
            $payment->order_id = $order->order_id;
            $payment->midtrans_order_id = $midtrans_order->token;
            $payment->midtrans_transaction_id = $order->order_id;
            $payment->total_price = $data['price']['net_price'];
            $payment->shipping_price = $order->shipping_cost;

            $payment->save();

            // $cookie = \Cookie::forget('cart');

            // Mail::to($request->email)->send(new OrderStatus($order, 1));
            return ['status'=>true,'payment_page'=>$midtrans_order->redirect_url,'order_id'=>$order->order_id];
        } catch (Exception $e) {
            return Response::json($e->getMessage());
        }
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return $snapToken;

    }

    public function notificationAPI(Request $request)
    {
        $payment = Payment::where('order_id', $request->order_id)
            ->update(['status' => $request->transaction_status]);
        return $payment;

        //return "Halo";
        // Set your Merchant Server Key
        // \Midtrans\Config::$isProduction = $this->isProduction();
        // \Midtrans\Config::$serverKey = $this->isProduction() ? $this->serverKeyProd : $this->serverKeyStag;
        // \Midtrans\Config::$isSanitized = $this->isSanitized;
        // \Midtrans\Config::$is3ds = $this->is3ds;

        // $orderId = $request->order;
        // $status = \Midtrans\Transaction::status($orderId);
        // return Response::json($status);
        // var_dump($status);
    }
    public function postNotificationAPI(Request $request)
    {
        $payment = Payment::where('order_id', $request->order_id)
            ->update(['status' => $request->transaction_status]);
        return $payment;
        // Set your Merchant Server Key
        // \Midtrans\Config::$isProduction = $this->isProduction();
        // \Midtrans\Config::$serverKey = $this->isProduction() ? $this->serverKeyProd : $this->serverKeyStag;
        // \Midtrans\Config::$isSanitized = $this->isSanitized;
        // \Midtrans\Config::$is3ds = $this->is3ds;

        // $orderId = $request->order;
        // $status = \Midtrans\Transaction::status($orderId);
        // return Response::json($status);
        // var_dump($status);
    }

    private function isProduction()
    {
        return config('app.env') == 'production' ? true : false;
    }

}
