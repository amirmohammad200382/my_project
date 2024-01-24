<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements( 'id');
            $table->string('title' ,255);
            $table->integer('price')->unsigned();
            $table->integer('inventory')->unsigned();
            $table->text('description')->collation( 'utf8mb4_general_ci');
            $table->timestamp( 'created_at')->useCurrent();
            $table->timestamp( 'updated_at')->useCurrent();
            $table->enum('status' , (['enable' , 'disable']));
            $table->foreignId( 'user_id');
            $table->foreign( 'user_id')
                ->references( 'id')
                ->on( 'users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
