<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 
        'amount', 
        'created_by', 
        'split_method',
        'expense_date'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_expenses')
            ->withPivot('share', 'amount_paid')
            ->withTimestamps();
    }
}
