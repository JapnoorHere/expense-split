<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use App\Models\UserExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('created_at', 'desc')->get();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $users = User::all();
        return view('expenses.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'users' => 'required|array|min:1',
            'split_method' => 'required|in:equal,custom',
        ]);

        DB::beginTransaction();
        
        try {
            $expense = Expense::create([
                'description' => $request->description,
                'amount' => $request->amount,
                'created_by' => Auth::id(),
                'split_method' => $request->split_method,
                'expense_date' => $request->expense_date,
            ]);

            $selectedUsers = $request->users;
            $totalUsers = count($selectedUsers);
            
            if ($request->split_method === 'equal') {
                $sharePerUser = $request->amount / $totalUsers;
                
                foreach ($selectedUsers as $userId) {
                    UserExpense::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'share' => $sharePerUser,
                        'amount_paid' => ($userId == Auth::id()) ? $request->amount : 0,
                    ]);
                }
            } else {
                // Custom split
                $totalPercentage = 0;
                
                foreach ($selectedUsers as $userId) {
                    $percentKey = 'percentage_' . $userId;
                    if ($request->has($percentKey)) {
                        $totalPercentage += $request->$percentKey;
                    }
                }
                
                if ($totalPercentage != 100) {
                    throw new \Exception('Total percentage must equal 100%');
                }
                
                foreach ($selectedUsers as $userId) {
                    $percentKey = 'percentage_' . $userId;
                    $percentage = $request->$percentKey;
                    $share = ($percentage / 100) * $request->amount;
                    
                    UserExpense::create([
                        'user_id' => $userId,
                        'expense_id' => $expense->id,
                        'share' => $share,
                        'amount_paid' => ($userId == Auth::id()) ? $request->amount : 0,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('expenses.index')
                ->with('success', 'Expense created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating expense: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $users = User::all();
        return view('expenses.edit', compact('expense', 'users'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ]);

        // Simple update - just updating description, amount and date
        // We're not handling changes to users or split methods to keep this simpler
        $expense->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        // Check if the current user created this expense
        if ($expense->created_by !== Auth::id()) {
            return redirect()->route('expenses.index')
                ->with('error', 'You can only delete expenses that you created.');
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }
}
