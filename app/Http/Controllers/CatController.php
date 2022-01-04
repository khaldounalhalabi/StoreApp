<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException ; 
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler ;
use voku\helper\ASCII;
use Illuminate\Database\Eloquent\Model ; 

class CatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() // return all categories from database as json
    {    
        $categories = Cat::orderBy("name")->get() ; //get all categories from the database and store it in categories variable
        return response()->json([$categories , 'message'=> 'data has been retrieved']) ; 
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
            'name' => 'required|unique:cats|min:3|max:25' , //rule for entering the name
        ] ; 
        $validator = Validator::make($request->all() , $rules) ; 
        
        if($validator->fails())
        {
            return response()->json(['error message'=>$validator->errors()]) ; 
        }
        try
        {
            $data = $request->input() ; 
            $category = new Cat ; 
            $category->name = $data['name'] ; 
            if($category->save()) 
            {
                return response()->json(['message'=>'data has been saved']) ; 
            } 
        }
        catch(\Exception $e)
        {
            return response()->json(['message'=>'Thers is been an error' , 'error message'=>$e->getMessage()]) ; 
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cat  $cat
     * @return \Illuminate\Http\Response
     */
    public function show(Cat $cat , $id)
    {
        return response()->json([Cat::find($id) , 'message'=>'element has been retrieved']) ; 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cat  $cat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cat $cat , $id)
    {
        $rules = 
        [
            'name' => 'required|unique:cats|min:3|max:25' , 
        ] ; 
        $validator = Validator::make($request->all() , $rules) ; 
        
        if($validator->fails())
        {
            return response()->json(['error message'=>$validator->errors()]) ; 
        }
        try
        {
            $data = $request->input() ; 
            $cat = Cat::find($id) ;
            $cat->name = $data['name'] ;   
            $cat->save() ; 
            return response()->json(['message'=>'data has been updated']) ;   
        }
        catch(\Exception $e)
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$e->getMessage()]) ; 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cat  $cat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cat $cat , $id)
    {
        try
        {
            $cat = Cat::find($id) ; 
            $cat->delete() ;
            return response()->json(['message'=>'item has been deleted']) ;  
        }
        catch(\Exception $e)
        {
            return response()->json(['message'=>'there is been an error' , 'error message'=>$e->getMessage()]) ; 
        }
    }
}