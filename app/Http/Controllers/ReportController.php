<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Expense;
use App\Models\UserExpense;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $users = User::all();
        $balances = [];
        
        foreach ($users as $user) {
            $balances[$user->id] = [
                'user' => $user,
                'owes' => [],
                'owed' => [],
                'total_owes' => 0,
                'total_owed' => 0
            ];
        }
        
        // Get expenses
        $expenses = Expense::with('users')->get();
        
        foreach ($expenses as $expense) {
            $paidByUserId = $expense->created_by;
            
            foreach ($expense->users as $user) {
                if ($user->id === $paidByUserId) {
                    continue; // Skip the person who paid
                }
                
                $share = $user->pivot->share;
                
                // User owes the payer
                if (!isset($balances[$user->id]['owes'][$paidByUserId])) {
                    $balances[$user->id]['owes'][$paidByUserId] = 0;
                }
                $balances[$user->id]['owes'][$paidByUserId] += $share;
                $balances[$user->id]['total_owes'] += $share;
                
                // Payer is owed by this user
                if (!isset($balances[$paidByUserId]['owed'][$user->id])) {
                    $balances[$paidByUserId]['owed'][$user->id] = 0;
                }
                $balances[$paidByUserId]['owed'][$user->id] += $share;
                $balances[$paidByUserId]['total_owed'] += $share;
            }
        }
        
        // Calculate simplified debts (who really owes what to whom)
        $simplifiedDebts = [];
        
        foreach ($users as $user1) {
            foreach ($users as $user2) {
                if ($user1->id === $user2->id) continue;
                
                $user1OwesToUser2 = isset($balances[$user1->id]['owes'][$user2->id]) 
                    ? $balances[$user1->id]['owes'][$user2->id] 
                    : 0;
                    
                $user2OwesToUser1 = isset($balances[$user2->id]['owes'][$user1->id]) 
                    ? $balances[$user2->id]['owes'][$user1->id] 
                    : 0;
                
                $netDebt = $user1OwesToUser2 - $user2OwesToUser1;
                
                if ($netDebt > 0) {
                    $simplifiedDebts[] = [
                        'from' => $user1,
                        'to' => $user2,
                        'amount' => $netDebt
                    ];
                }
            }
        }
        
        return view('reports.index', compact('balances', 'simplifiedDebts'));
    }
}
