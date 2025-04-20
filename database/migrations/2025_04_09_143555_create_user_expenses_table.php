<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('user_expenses', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('expense_id');
        $table->decimal('share', 10, 2);
        $table->decimal('amount_paid', 10, 2)->default(0);
        $table->timestamps();
        
        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_expenses');
    }
};
