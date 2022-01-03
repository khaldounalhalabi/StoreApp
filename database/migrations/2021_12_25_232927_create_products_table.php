<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id');
            $table->string('name') ; 
            $table->float('price') ; 
            $table->string('description') ; 
            $table->date('expiration_date') ; 
            $table->date('discount_date1') ;
            $table->date('discount_date2') ;
            $table->date('discount_date3') ; 
            $table->integer('discount1') ; 
            $table->integer('discount2') ; 
            $table->integer('discount3') ;
            $table->float('price1') ; 
            $table->float('price2') ;  
            $table->float('price3') ;  
            $table->string('image_url') ; 
            $table->integer('quantity')->default(1) ;  
            $table->string('category') ; 
            $table->integer('likes')->default(0) ; 
            $table->integer('views')->default(0) ; 
            $table->json('owner') ; 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
