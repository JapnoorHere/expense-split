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
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->string('description');
        $table->decimal('amount', 10, 2);
        $table->unsignedBigInteger('created_by');
        $table->enum('split_method', ['equal', 'custom']);
        $table->date('expense_date');
        $table->timestamps();
        
        $table->foreign('created_by')->references('id')->on('users');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
