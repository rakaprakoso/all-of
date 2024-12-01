<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
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

Route::get('/keeus/order/{order_id}', [OrderController::class, 'show']);

Route::get('/keeus/order2', function () {
    $data = File::get(public_path('json/keeus_order.json'));
    return response()->json(json_decode($data, true));
});
Route::get('/keeus/product', function () {
    $merchantId = 1;
    return  app(ProductController::class)->index(request(), $merchantId);
});
Route::get('/keeus/product2', function () {
    $data = File::get(public_path('json/keeus_product.json'));
    return response()->json(json_decode($data, true));
});

Route::get('/keeus/story', function () {
    //echo env('APP_ENV', 'dev');
    if (env('APP_ENV', 'production') === 'production') {
        $directoryPath = public_path('../../public_html/storage/img/keeus_story');

        if (!File::exists($directoryPath)) {
            return response()->json(['error' => 'Directory not found'], 404);
        }

        // Get all files in the directory
        $files = File::files($directoryPath);

        // Store the file names in an array
        $fileNames = [];
        foreach ($files as $file) {
            $fileNames[] = "https://storage.deprakoso.com/img/keeus_story/".$file->getFilename(); // Get the file name (you can also get the full path if needed)
        }

        // Return the list of filenames as a JSON response
        return response()->json($fileNames);
    } else {
        //  return response()->json([]);
        $apiUrl = 'https://allof.deprakoso.com/api/keeus/story';
        // Make the HTTP GET request
        $response = Http::get($apiUrl);
        // Check if the response is successful
        if ($response->successful()) {
            // Convert the response JSON to an array
            $data = $response->json();
        }else {
            // Handle errors
            return ['error' => 'Failed to fetch data from API'];
        }
        return response()->json($data);
    }

    // Check if the directory exists
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
Route::get('/vendors', [EcommerceController::class, 'getVendorList'])->middleware('auth:sanctum');
// Route::get('/order/{id}', [EcommerceController::class, 'showOrder'])->middleware('auth:sanctum');
Route::get('/payment/notification', [EcommerceController::class, 'notificationAPI'])->name('notificationAPI');
Route::post('/payment/notification', [EcommerceController::class, 'postNotificationAPI'])->name('postNotificationAPI');
