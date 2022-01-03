<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products"  ;
    protected $primaryKey = "id" ;
    public $timestamps = true ;
    protected $fillable = 
    [
        'name' ,
        'price' , 
        'description' , 
        'expiration_date' , 
        'image_url' , 
        'quantity' , 
        'category ' , 
        'owner' , 
        'likes' , 
        'views' , 
        'discount_date1' , 
        'discount_date2' ,     
        'discount_date3' ,     
        'discount1' , 
        'discount2' , 
        'discount3' , 
        'price1' , 
        'price2' , 
        'price3' , 

    ] ; 

    public function comments()
    {
        return $this->hasMany(Comment::class) ; 
    }
}
