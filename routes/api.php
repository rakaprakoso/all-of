<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\EcommerceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/data', function () {
    $data = File::get(public_path('json/data.json'));
    return response()->json(json_decode($data, true));
});

//API route for register new user
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

//Protecting Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', function(Request $request) {
        $responseMessage = "user profile";
        $data = auth()->user();
        return response()->json([
            "success" => true,
            "message" => $responseMessage,
            "data" => $data
        ], 200);

        // return response()
        //     ->json([
        //         'success' => true,
        //         'user_data' => auth()->user(),
        //     ]);
    });

    // API route for logout user
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/contact-portfolio', [App\Http\Controllers\SendMailController::class, 'contact']);

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);

Route::get('/category', [EcommerceController::class, 'category']);
Route::get('/cart', [EcommerceController::class, 'getCart'])->middleware('auth:sanctum');
Route::post('/cart', [EcommerceController::class, 'postCart'])->middleware('auth:sanctum');
Route::get('/checkoutaddress', [EcommerceController::class, 'getCheckoutAddress'])->middleware('auth:sanctum');

Route::post('/placeorder', [EcommerceController::class, 'placeOrder'])->middleware('auth:sanctum');
Route::get('/order', [EcommerceController::class, 'indexOrder'])->middleware('auth:sanctum');
Route::get('/order/{id}', [EcommerceController::class, 'showOrder'])->middleware('auth:sanctum');
Route::get('/payment/notification', [EcommerceController::class, 'notificationAPI'])->name('notificationAPI');
Route::post('/payment/notification', [EcommerceController::class, 'postNotificationAPI'])->name('postNotificationAPI');
