@extends('layouts.app')

@section('content')
<h1 class="page-title">Dashboard</h1>

<div class="row">
    <div class="col" style="flex: 2;">
        <div class="card">
            <div class="card-header">Welcome, {{ Auth::user()->name }}!</div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: rgba(34, 197, 94, 0.1); color: #16a34a;">💰</div>
                            <div class="stat-content">
                                <h3>Others Owe You</h3>
                                <p>${{ number_format($amountOwedToUser, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;">💸</div>
                            <div class="stat-content">
                                <h3>You Owe Others</h3>
                                <p>${{ number_format($amountUserOwes, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(99, 102, 241, 0.1); color: #6366f1;">⚖️</div>
                    <div class="stat-content">
                        <h3>Net Balance</h3>
                        <p class="{{ $amountOwedToUser > $amountUserOwes ? 'positive' : ($amountOwedToUser < $amountUserOwes ? 'negative' : '') }}">
                            ${{ number_format($amountOwedToUser - $amountUserOwes, 2) }}
                        </p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('expenses.create') }}" class="btn">Add New Expense</a>
                    <a href="{{ route('reports') }}" class="btn btn-secondary" style="margin-left: 10px;">View Reports</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col" style="flex: 3;">
        <div class="card">
            <div class="card-header">Recent Expenses</div>
            <div class="card-body">
                @if($recentExpenses->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Split</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentExpenses as $expense)
                                    <tr>
                                        <td>{{ $expense->description }}</td>
                                        <td>{{ date('M d, Y', strtotime($expense->expense_date)) }}</td>
                                        <td>${{ number_format($expense->amount, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $expense->split_method === 'equal' ? 'badge-equal' : 'badge-custom' }}">
                                                {{ ucfirst($expense->split_method) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('expenses.index') }}" class="btn">View All Expenses</a>
                    </div>
                @else
                    <div class="text-center" style="padding: 30px 0;">
                        <div style="font-size: 3rem; margin-bottom: 10px;">📋</div>
                        <p style="color: var(--light-text); margin-bottom: 20px;">No recent expenses found.</p>
                        <a href="{{ route('expenses.create') }}" class="btn">Add Your First Expense</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Quick Tips</div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div style="text-align: center; padding: 10px;">
                    <div style="font-size: 2rem; margin-bottom: 10px;">💡</div>
                    <h3 style="margin-bottom: 10px; color: var(--primary-color);">Add Expenses</h3>
                    <p>Track what you spend with friends and split costs evenly or custom percentages.</p>
                </div>
            </div>
            <div class="col">
                <div style="text-align: center; padding: 10px;">
                    <div style="font-size: 2rem; margin-bottom: 10px;">📊</div>
                    <h3 style="margin-bottom: 10px; color: var(--primary-color);">Check Reports</h3>
                    <p>See who owes what and keep track of all balances between friends.</p>
                </div>
            </div>
            <div class="col">
                <div style="text-align: center; padding: 10px;">
                    <div style="font-size: 2rem; margin-bottom: 10px;">🤝</div>
                    <h3 style="margin-bottom: 10px; color: var(--primary-color);">Settle Up</h3>
                    <p>Use the reports page to see simplified payments to settle all debts easily.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
