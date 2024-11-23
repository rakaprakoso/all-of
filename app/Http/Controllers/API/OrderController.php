<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$merchantId=null)
    {
        $responseMessage = '';
        try {
            if ($merchantId) {
                if ($request->random) {
                    $products = Product::inRandomOrder()
                        ->where('slug', '!=', $request->slug)
                        ->when($merchantId, function ($query, $merchantID) {
                            $query->where('merchant_id', $merchantID);
                        })
                        ->take(3)
                        ->get();
                } elseif ($request->hero == 'true') {
                    $products = Product::where('hero', true)
                        ->when($merchantId, function ($query, $merchantID) {
                            $query->where('merchant_id', $merchantID);
                        })
                        ->orderBy('updated_at', 'desc')
                        ->first();
                } else {
                    $products = Product::show()
                        ->when($merchantId, function ($query, $merchantID) {
                            $query->where('merchant_id', $merchantID);
                        })
                        ->orderBy('updated_at', 'desc')
                        ->paginate(24);
                }
            }else{
                if($request->random){
                    $products = Product::inRandomOrder()->where('slug', '!=', $request->slug)->take(3)->get();
                }elseif($request->hero == 'true'){
                    $products = Product::where('hero', true)->first();
                }else{
                    // $products = Product::show()->inRandomOrder()->paginate(24);
                    $products = Product::show()->paginate(24);
                }
            }
            $responseMessage = 'Product Get!';
            return $this->responseSuccess($responseMessage, $products);
        } catch (\Throwable $th) {
            echo $th->getMessage();
            $responseMessage = 'Product Fail!';
            return $this->responseFail($responseMessage, null);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($order_id)
    {
        $responseMessage = '';
        try {
            $order = Order::where('order_id',$order_id)->first();
            // $order['imagesArr'] = [$order->thumbnail_img];
            // $order['imagesArr'] = array_merge($order['imagesArr'],json_decode(json_encode($order->images->pluck('image_path')), true));

            $responseMessage = 'Order Get Successfully!';
            return $this->responseSuccess($responseMessage, $order);
        } catch (\Throwable $th) {
            echo $th;
            $responseMessage = 'Order Error!';
            return $this->responseFail($responseMessage, null);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
