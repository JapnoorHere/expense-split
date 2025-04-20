<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use App\Models\UserExpense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get total amount owed to this user
        $amountOwedToUser = DB::table('user_expenses as ue1')
            ->join('expenses', 'expenses.id', '=', 'ue1.expense_id')
            ->join('user_expenses as ue2', 'expenses.id', '=', 'ue2.expense_id')
            ->where('expenses.created_by', $user->id)
            ->where('ue2.user_id', '!=', $user->id)
            ->sum('ue2.share');
            
        // Get total amount this user owes to others
        $amountUserOwes = DB::table('user_expenses as ue')
            ->join('expenses', 'expenses.id', '=', 'ue.expense_id')
            ->where('expenses.created_by', '!=', $user->id)
            ->where('ue.user_id', $user->id)
            ->sum('ue.share');
        
        // Get recent expenses involving this user
        $recentExpenses = Expense::whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', [
            'amountOwedToUser' => $amountOwedToUser,
            'amountUserOwes' => $amountUserOwes,
            'recentExpenses' => $recentExpenses
        ]);
    }
}
