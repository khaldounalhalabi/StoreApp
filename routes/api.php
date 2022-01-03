<?php

use App\Models\Product;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//Products Controllers API
Route::middleware(['auth:api'])->group(
    function() 
    {
        /*

            if you sent with the request a name it will show you all the products with same name
            if you sent with the request a price_from parameter and a price_to parameter it will gives you the products within this prices
            if you sent with the request a expiration_date_from and a expiration_date_to it will gives you the products within that has an expiration date within this two values
            if you sent with the request a category it will gives you all products with this within this category
            
            any value of the above is nullable and this will gives you all the products ordered by creating date

            all values within this route should be sent as following :
            name
            category
            price_from
            price_to 
            expiration_date_from
            expiration_date_to 

        */
        Route::get('/products/index' ,'App\Http\Controllers\ProductController@index') ;
        /*
            This Route is for creating a new product
            all data can send within the request 
            this rules must be observed when entering data
                'name'=>'required|min:3|max:25|stirng' , 
                'price' =>'required|numeric' , 
                'description' => 'string|null|min:12' , 
                'expiration_date' => 'date_format:dd/mm/yyyy|after:tomorrow' , 
                'image_url' => 'string|required|min:3' , 
                'quantity' => 'numeric|required' , 
                'category' => 'exists:cats,name|required' , 
        */  
        Route::post('/products/store', 'App\Http\Controllers\ProductController@store');
        /*
            This Route is for editing a product
            with a givin peoduct id
            you should send the product id as an id parameter within the request
            all data can send within the request 
            this rules must be observed when entering data
                'name'=>'required|min:3|max:25|stirng' , 
                'price' =>'required|numeric' , 
                'description' => 'string|null|min:12' , 
                'expiration_date' => 'date_format:dd/mm/yyyy|after:tomorrow' , 
                'image_url' => 'string|required|min:3' , 
                'quantity' => 'numeric|required' , 
                'category' => 'exists:cats,name|required' , 
        */  
        Route::put('/products/update/{id}', 'App\Http\Controllers\ProductController@update');
        /** 
         * This Route is for showing a product with a specified id
         * you should send the product id as an id parameter within the request
        */
        Route::get('/products/show/{id}', 'App\Http\Controllers\ProductController@show');
        /**
         * This Route is for deleting a product with a specified id
         * you should send the product id as an id parameter within the request
         */
        Route::delete('/products/delete/{id}', 'App\Http\Controllers\ProductController@destroy');


        //Products Comments API


        /**
         * This Route is for adding a comment to a specified product 
         * you have to send the product id as an id parameter and the comment you want to add within the request
         * comment is required and the max of it is 1000 charcter
         */
        Route::post('/products/add_comment/{id}' ,'App\Http\Controllers\ProductController@add_comment' ) ;
        
        /**
         * this route is for deleting a specified comment from a specified product
         * you have to send the product id as an id parameter within the request
         * you have to send the comment id within the request
         */
        Route::delete('/products/delete_comment' ,'App\Http\Controllers\ProductController@delete_comment' ) ;
        /**
         * This Route is for editing a specified comment in a specified product 
         * the product id and comment id should send within the request
         * the new comment must send within the request 
         * comment is required and the max of it is 1000 charcter
         */ 
        Route::put('/products/edit_comment' ,'App\Http\Controllers\ProductController@edit_comment' ) ;
        /**
         * this route is for showing all comment of a specified product
         * you should send the product id within the request
         */ 
        Route::get('/products/show_comments' ,'App\Http\Controllers\ProductController@show_comments' ) ; 



        //like api
        
        //add like
        Route::post('/products/add_like/{id}','App\Http\Controllers\ProductController@add_like' ) ; 

        //show likes
        Route::get('/products/show_likes/{id}','App\Http\Controllers\ProductController@show_likes' ) ;


        //views API

        //add view
        Route::post('/products/add_view/{id}','App\Http\Controllers\ProductController@add_view' ) ;

        //show views
        Route::get('/products/show_views/{id}','App\Http\Controllers\ProductController@show_views' ) ;
    }
); 


// Categories Controllers API

        Route::get('/cats/index' ,'App\Http\Controllers\CatController@index')->middleware(['auth:api']) ;
        Route::post('/cats/store' , 'App\Http\Controllers\CatController@store') ;
        Route::put('/cats/update/{id}' , 'App\Http\Controllers\CatController@update') ;
        Route::get('/cats/show/{id}' , 'App\Http\Controllers\CatController@show') ;
        Route::delete('/cats/delete/{id}' , 'App\Http\Controllers\CatController@destroy') ;

//Authentecation Controllers API

        Route::post('/users/login' ,'App\Http\Controllers\AuthController@login'); 
        Route::post('/users/register' , 'App\Http\Controllers\AuthController@register') ; 
        Route::post('/users/logout', 'App\Http\Controllers\AuthController@logout')->middleware('auth:api') ;  
        Route::post('/users/user_details' ,'App\Http\Controllers\AuthController@user_details')->middleware('auth:api') ; 
     


