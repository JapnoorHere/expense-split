<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\UserExpense;
use App\Models\User;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run()
    {
        // Get users
        $users = User::all();
        
        // Create some sample expenses
        $expenses = [
            [
                'description' => 'Dinner at Restaurant',
                'amount' => 120.00,
                'created_by' => 1, // John
                'split_method' => 'equal',
                'expense_date' => Carbon::now()->subDays(5),
                'participants' => [1, 2, 3], // John, Jane, Mike
            ],
            [
                'description' => 'Movie tickets',
                'amount' => 60.00,
                'created_by' => 2, // Jane
                'split_method' => 'equal',
                'expense_date' => Carbon::now()->subDays(3),
                'participants' => [1, 2, 4], // John, Jane, Sarah
            ],
            [
                'description' => 'Groceries',
                'amount' => 85.50,
                'created_by' => 3, // Mike
                'split_method' => 'equal',
                'expense_date' => Carbon::now()->subDays(2),
                'participants' => [2, 3, 4], // Jane, Mike, Sarah
            ],
            [
                'description' => 'Utility Bills',
                'amount' => 150.00,
                'created_by' => 4, // Sarah
                'split_method' => 'custom',
                'expense_date' => Carbon::now()->subDay(),
                'participants' => [1, 2, 3, 4], // All
                'custom_shares' => [25, 25, 25, 25], // Equal percentages
            ],
        ];

        foreach ($expenses as $expenseData) {
            $participants = $expenseData['participants'];
            $expense = Expense::create([
                'description' => $expenseData['description'],
                'amount' => $expenseData['amount'],
                'created_by' => $expenseData['created_by'],
                'split_method' => $expenseData['split_method'],
                'expense_date' => $expenseData['expense_date'],
            ]);
            
            if ($expenseData['split_method'] === 'equal') {
                $sharePerUser = $expenseData['amount'] / count($participants);
                
                foreach ($participants as $userId) {
                    UserExpense::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'share' => $sharePerUser,
                        'amount_paid' => ($userId == $expenseData['created_by']) ? $expenseData['amount'] : 0,
                    ]);
                }
            } else {
                // Custom split
                foreach ($participants as $index => $userId) {
                    $percentage = $expenseData['custom_shares'][$index];
                    $share = ($percentage / 100) * $expenseData['amount'];
                    
                    UserExpense::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'share' => $share,
                        'amount_paid' => ($userId == $expenseData['created_by']) ? $expenseData['amount'] : 0,
                    ]);
                }
            }
        }
    }
}
