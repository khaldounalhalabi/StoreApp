<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table = "comments"  ;
    protected $primaryKey = "id" ;
    public $timestamps = true ;
    protected $fillable = 
    [
        'comment' , 
        'user_id' , 
        'product_id' , 
    ] ; 
    public function products()
    {
        return $this->belongsTo(Product::class , 'foreign key');
    }

    public function users()
    {
        return $this->belongsTo(User::class , 'foreign key') ; 
    }
   
}
