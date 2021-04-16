<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGernetCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gernet_charges', function(Blueprint $table){
            $table->increments('id');            
            $table->foreignId('sale_id');//->references('id')->on('sales');
            $table->foreignId('checkout_id');//->references('id')->on('checkouts');
            $table->string('txid',35);
            $table->integer('location_id')->default(0);
            $table->string('location',100)->nullable();
            $table->string('status',10);            
            $table->timestamps();            
        });    
        
        DB::statement("ALTER TABLE gernet_charges 
        ADD CONSTRAINT `fk_sale_id` FOREIGN KEY ( `sale_id` ) REFERENCES `sales` ( `id` ),
        ADD CONSTRAINT `fk_checkout_id` FOREIGN KEY ( `checkout_id` ) REFERENCES `checkouts` ( `id` )  ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gernet_charges');
    }
}
