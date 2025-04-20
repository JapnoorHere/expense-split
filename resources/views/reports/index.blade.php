@extends('layouts.app')

@section('content')
<h1 class="page-title">Expense Reports</h1>

<div class="row">
    <div class="col" style="flex: 1;">
        <div class="card">
            <div class="card-header">Your Balance Summary</div>
            <div class="card-body">
                @php
                    $currentUser = Auth::user();
                    $currentUserBalance = $balances[$currentUser->id] ?? null;
                    $totalOwed = $currentUserBalance ? $currentUserBalance['total_owed'] : 0;
                    $totalOwes = $currentUserBalance ? $currentUserBalance['total_owes'] : 0;
                    $netBalance = $totalOwed - $totalOwes;
                @endphp
                
                <div class="balance-overview">
                    <div class="balance-amount {{ $netBalance >= 0 ? 'positive' : 'negative' }}">
                        ${{ number_format(abs($netBalance), 2) }}
                    </div>
                    <div class="balance-label">
                        @if($netBalance > 0)
                            You are owed money
                        @elseif($netBalance < 0)
                            You owe money
                        @else
                            You're all settled up
                        @endif
                    </div>
                </div>
                
                <div class="row" style="margin-top: 30px;">
                    <div class="col">
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: rgba(34, 197, 94, 0.1); color: #16a34a;">💰</div>
                            <div class="stat-content">
                                <h3>Others Owe You</h3>
                                <p>${{ number_format($totalOwed, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;">💸</div>
                            <div class="stat-content">
                                <h3>You Owe Others</h3>
                                <p>${{ number_format($totalOwes, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <a href="{{ route('expenses.create') }}" class="btn">Add New Expense</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col" style="flex: 2;">
        <div class="card">
            <div class="card-header">Simplified Settlement</div>
            <div class="card-body">
                @if(count($simplifiedDebts) > 0)
                    <p class="card-description">Here's the most efficient way to settle all debts:</p>
                    
                    <div class="settlement-container">
                        @foreach($simplifiedDebts as $debt)
                            <div class="settlement-item">
                                <div class="settlement-avatars">
                                    <div class="settlement-avatar" style="background-color: {{ '#' . substr(md5($debt['from']->id), 0, 6) }};">
                                        {{ strtoupper(substr($debt['from']->name, 0, 1)) }}
                                    </div>
                                    <div class="settlement-arrow">→</div>
                                    <div class="settlement-avatar" style="background-color: {{ '#' . substr(md5($debt['to']->id), 0, 6) }};">
                                        {{ strtoupper(substr($debt['to']->name, 0, 1)) }}
                                    </div>
                                </div>
                                
                                <div class="settlement-details">
                                    <div class="settlement-users">
                                        <span class="from-user">{{ $debt['from']->name }}</span>
                                        <span class="to-user">{{ $debt['to']->name }}</span>
                                    </div>
                                    <div class="settlement-amount">${{ number_format($debt['amount'], 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center" style="padding: 40px 0;">
                        <div style="font-size: 3rem; margin-bottom: 15px;">🎉</div>
                        <h3 style="margin-bottom: 10px; color: var(--text-color);">All Settled Up!</h3>
                        <p style="color: var(--light-text); max-width: 400px; margin-left: auto; margin-right: auto;">
                            There are no outstanding debts between users. Everyone is all squared up!
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 30px;">
    <div class="card-header">Detailed Balances</div>
    <div class="card-body">
        <div class="row">
            @foreach($balances as $userId => $balance)
                @if($balance['total_owes'] > 0 || $balance['total_owed'] > 0)
                    <div class="col" style="flex: 0 0 33.333%; margin-bottom: 20px;">
                        <div class="user-balance-card">
                            <div class="user-balance-header">
                                <div class="user-avatar" style="background-color: {{ '#' . substr(md5($balance['user']->id), 0, 6) }};">
                                    {{ strtoupper(substr($balance['user']->name, 0, 1)) }}
                                </div>
                                <h3>{{ $balance['user']->name }}</h3>
                                @if($balance['user']->id == Auth::id())
                                    <span class="user-badge">You</span>
                                @endif
                            </div>
                            
                            <div class="user-balance-body">
                                @php
                                    $netUserBalance = $balance['total_owed'] - $balance['total_owes'];
                                @endphp
                                
                                <div class="user-balance-summary {{ $netUserBalance >= 0 ? 'positive' : 'negative' }}">
                                    <span class="balance-label">Net Balance:</span>
                                    <span class="balance-value">${{ number_format($netUserBalance, 2) }}</span>
                                </div>
                                
                                @if($balance['total_owes'] > 0)
                                    <div class="user-balance-section">
                                        <h4>Owes:</h4>
                                        <ul class="user-balance-list">
                                            @foreach($balance['owes'] as $owedToUserId => $amount)
                                                <li>
                                                    <span>To <strong>{{ $balances[$owedToUserId]['user']->name }}</strong></span>
                                                    <span>${{ number_format($amount, 2) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="user-balance-total">
                                            <span>Total</span>
                                            <span>${{ number_format($balance['total_owes'], 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($balance['total_owed'] > 0)
                                    <div class="user-balance-section">
                                        <h4>Is Owed:</h4>
                                        <ul class="user-balance-list">
                                            @foreach($balance['owed'] as $owedByUserId => $amount)
                                                <li>
                                                    <span>From <strong>{{ $balances[$owedByUserId]['user']->name }}</strong></span>
                                                    <span>${{ number_format($amount, 2) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="user-balance-total">
                                            <span>Total</span>
                                            <span>${{ number_format($balance['total_owed'], 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
.card-description {
    color: var(--light-text);
    margin-bottom: 20px;
}

.balance-overview {
    text-align: center;
    padding: 20px 0;
}

.balance-amount {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.balance-amount.positive {
    color: #16a34a;
}

.balance-amount.negative {
    color: #dc2626;
}

.balance-label {
    font-size: 1.1rem;
    color: var(--text-color);
    font-weight: 500;
}

.settlement-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.settlement-item {
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    border: 1px solid var(--card-border);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.settlement-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

.settlement-avatars {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: linear-gradient(to right, rgba(99, 102, 241, 0.05), rgba(249, 115, 22, 0.05));
}

.settlement-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.settlement-arrow {
    margin: 0 20px;
    font-size: 1.5rem;
    color: var(--primary-color);
}

.settlement-details {
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--card-border);
}

.settlement-users {
    display: flex;
    flex-direction: column;
}

.from-user {
    font-weight: 500;
    margin-bottom: 5px;
}

.to-user {
    color: var(--light-text);
    font-size: 0.9rem;
}

.to-user:before {
    content: "to ";
}

.settlement-amount {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-color);
}

.user-balance-card {
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    border: 1px solid var(--card-border);
    overflow: hidden;
    height: 100%;
}

.user-balance-header {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid var(--card-border);
    background-color: rgba(99, 102, 241, 0.05);
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    margin-right: 10px;
}

.user-balance-header h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.user-badge {
    background-color: rgba(99, 102, 241, 0.1);
    color: var(--primary-color);
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 600;
    margin-left: 8px;
}

.user-balance-body {
    padding: 15px;
}

.user-balance-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.user-balance-summary.positive {
    background-color: rgba(34, 197, 94, 0.1);
}

.user-balance-summary.negative {
    background-color: rgba(239, 68, 68, 0.1);
}

.user-balance-summary .balance-label {
    font-size: 0.9rem;
    font-weight: 500;
}

.user-balance-summary .balance-value {
    font-weight: 600;
}

.user-balance-summary.positive .balance-value {
    color: #16a34a;
}

.user-balance-summary.negative .balance-value {
    color: #dc2626;
}

.user-balance-section {
    margin-bottom: 15px;
}

.user-balance-section h4 {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 10px 0;
    color: var(--primary-color);
}

.user-balance-list {
    list-style: none;
    padding: 0;
    margin: 0 0 10px 0;
}

.user-balance-list li {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 0.9rem;
}

.user-balance-total {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-weight: 600;
    border-top: 1px solid #e2e8f0;
}
</style>
@endsection
