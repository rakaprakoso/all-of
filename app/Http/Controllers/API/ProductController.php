<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseMessage = '';
        try {
            if($request->random){
                $products = Product::inRandomOrder()->where('slug', '!=', $request->slug)->take(3)->get();
            }elseif($request->hero == 'true'){
                $products = Product::where('hero', true)->first();
            }else{
                // $products = Product::show()->inRandomOrder()->paginate(24);
                $products = Product::show()->paginate(24);
            }

            $responseMessage = 'Product Get!';
            return $this->responseSuccess($responseMessage, $products);
        } catch (\Throwable $th) {
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
    public function show($id)
    {
        $responseMessage = '';
        try {
            $product = Product::where('slug',$id)->first();
            $product['imagesArr'] = [$product->thumbnail_img];
            $product['imagesArr'] = array_merge($product['imagesArr'],json_decode(json_encode($product->images->pluck('image_path')), true));

            $responseMessage = 'Product Get Successfully!';
            return $this->responseSuccess($responseMessage, $product);
        } catch (\Throwable $th) {
            $responseMessage = 'Product Error!';
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
