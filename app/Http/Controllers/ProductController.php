<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException ; 
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler ;
use App\Models\Cat;
use App\Models\Comment;
use voku\helper\ASCII;
use Illuminate\Database\Eloquent\Model ; 
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use App\Models\User;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        
        $rules =                                          //rules if the user chose to use filter
        [
            'name' => 'nullable|string' , 
            'category' =>'nullable|string|exists:cats' , 
            'price_from' =>'nullable|numeric' , 
            'price_to'=>'nullable|numeric' , 
            'expiration_date_from' => 'nullable|date' , //format is : Y-M-D ex  : 2022-1-4
            'expiration_date_to' => 'nullable|date' ,  //format is : Y-M-D ex  : 2022-1-4
        ];

        $validator = Validator::make($request->all() , $rules) ;
        
        if($validator->fails())
        {
            return response()->json(["message" =>"there is been an error"  ,"error message" => $validator->errors()]) ; 
        }

        $name = $request->name ; 
        $category = $request->category ;
        $price_from = $request->price_from ; 
        $price_to = $request->price_to ; 
        $expiration_date_from = $request->expiration_date_from ; 
        $expiration_date_to = $request->expiration_date_to ; 
        
        try
        {
            $product_query = Product::query() ; 
            
            if(!is_null($name))
            {
                $product_query->where('name' , $name) ; 
            }
            if(!is_null($category))
            {
                $product_query->where('category' , $category) ; 
            }

            if(!is_null($price_from))
            {
                $product_query->where('price' , '>=' , $price_from) ; 
            }

            if(!is_null($price_to))
            {
                $product_query->where('price' , '<=' , $price_to) ; 
            }

            if(!is_null($expiration_date_from))
            {
                $product_query->where('expiration_date' , '>=' , $expiration_date_from) ;   
            }

            if(!is_null($expiration_date_to))
            {
                $product_query->where('expiration_date' , '<=' , $expiration_date_to) ; 
            }
            $products = $product_query->orderBy('created_at' , 'DESC')->get() ; 
            return response()->json([$products , 'message'=>'data has been retrieved']) ;
        }
        catch(\Exception $e)
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$e->getMessage()]) ; 
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
        $rules = 
        [
            'name'=>'required|min:3|max:25|string' , 
            'price' =>'required|numeric' , 
            'description' => 'string|min:12' , 
            'expiration_date' => 'after:+15 day' , 
            'image_url' => 'string|required|min:3' , 
            'quantity' => 'numeric|required' , 
            'category' => 'exists:cats,name|required' , 
            'discount_date1' => 'date|required' ,
            'discount_date2' => 'date|required' , 
            'discount_date3' => 'date|required' ,  
            'discount1' => ' numeric|required' , 
            'discount2' => ' numeric|required' , 
            'discount3' => ' numeric|required' , 
        ] ; 

        $validator = Validator::make($request->all() , $rules) ; 
        if($validator->fails())
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$validator->errors()]) ; 
        }

        try
        {
            $data = $request->input() ;
            $product = new Product  ; 
            $product->name = $data['name'] ; 
            $product->price = $data['price'] ; 
            $product->description = $data['description'] ; 
            $product->expiration_date = $data['expiration_date'] ; 
            $product->image_url = $data['image_url'] ; 
            $product->quantity = $data['quantity'] ; 
            $product->category = $data['category'] ;
            $product->discount_date1 = $data['discount_date1'] ; 
            $product->discount_date2 = $data['discount_date2'] ; 
            $product->discount_date3 = $data['discount_date3'] ;
            $product->discount1 = $data['discount1'] ; 
            $product->discount2 = $data['discount2'] ;
            $product->discount3 = $data['discount3'] ;
            $product->price1 = $product->price - ($product->price *($data['discount1']/100))  ; //price after first discount
            $product->price2 = $product->price - ($product->price *($data['discount2']/100)) ;  //price after second discount
            $product->price3 = $product->price - ($product->price *($data['discount3']/100)) ;  //price after third discount
            $product->owner = Auth::user() ; 
            $product->save() ; 

            return response()->json(['message'=>'data has been saved']) ; 
        }
        catch(\Exception $e)
        {
            return response()->json(["message"=>"there is been an error" , "error message" => $e->getMessage()]) ; 
        } 

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product , $id)
    {
        $product = Product::find($id) ; 
        if(now()>$product->discount_date1)
        {
            $product->price = $product->price1 ; 
        } 
        if(now()>$product->discount_date2)
        {
            $product->price = $product->price2 ; 
        }
        if(now()>$product->discount_date3)
        {
            $product->price = $product->price3 ;
        }
        return response()->json([$product , $product->comments , "message"=>"data has been retrieved"]) ; 
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product , $id)
    {
        $rules = 
        [
            'name'=>'required|min:3|max:25|string' , 
            'price' =>'required|numeric' , 
            'description' => 'string|min:12' , 
            'image_url' => 'string|required|min:3' , 
            'quantity' => 'numeric|required' , 
            'category' => 'exists:cats,name|required' , 
        ] ; 

        $validator = Validator::make($request->all() , $rules) ; 
        if($validator->fails())
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$validator->errors()]) ; 
        }

        try
        {
            $data = $request->input() ;
            $product = Product::find($id); 
            $product->name = $data['name'] ; 
            $product->price = $data['price'] ; 
            $product->description = $data['description'] ;  
            $product->image_url = $data['image_url'] ; 
            $product->quantity = $data['quantity'] ; 
            $product->category = $data['category'] ;
            $product->save() ; 

            return response()->json(['message'=>'data has been updated']) ; 
        }
        catch(\Exception $e)
        {
            return response()->json(["message"=>"there is been an error" , "error message" => $e->getMessage()]) ; 
        } 

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product , $id)
    {
        try
        {
            $product = Product::find($id) ; 
            $product->delete() ;
            return response()->json(['message'=>'item has been deleted']) ;  
        }
        catch(\Exception $e)
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$e->getMessage()]) ; 
        }
    }


    public function add_comment(Request $request , $id)
    {
        
        $validator = Validator::make($request->all() , ['comment' => 'required|max:1000']) ; 
        if($validator->fails())
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$validator->errors()]) ; 
        }
        try
        {
            $comment = new Comment ;  
            $comment->comment = $request->input('comment');
            $comment->user_id = Auth::id() ; 
            $comment->product_id = $id ; 
            $comment->save() ;
            return response()->json(['message'=>'comment has been added successfully']) ;  
        } 
        catch(\Exception $e)
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$e->getMessage()]) ; 
        }

    }

    public function delete_comment(Request $request ) 
    { 
        $comment_id = $request->input('comment_id') ;
        $product_id = $request->input('product_id') ;  
        $comment = Product::find($product_id)->comments()->find($comment_id)->delete() ; 
        return response()->json(['message'=>'comment has been deleted successfully']) ; 
    }

    public function edit_comment(Request $request )
    {
        $validator = Validator::make($request->all() , ['comment'=>'required|max:1000']) ; 
        if($validator->fails())
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$validator->errors()]) ; 
        }
        try
        {
            $comment = new Comment ; 
            $comment_id = $request->input('comment_id') ; 
            $product_id = $request->input('product_id') ; 
            $comment = Product::find($product_id)->comments()->find($comment_id) ; 
            $comment->comment = $request->input('comment') ; 
            $comment->save() ; 
            return response()->json(['message'=>'comment has been updated']) ; 
        }
        catch(\Exception $e)
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$e->getMessage()]) ; 
        }

    }

    public function show_comments(Request $request)
    {
        $product_id = $request->input('product_id') ; 
        $comments = Product::find($product_id)->comments ; 
        return response()->json(['message'=>'data has been retieved' , $comments]) ; 
    }
    

}
