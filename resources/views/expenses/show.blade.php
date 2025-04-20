@extends('layouts.app')

@section('content')
<div style="display: flex; align-items: center; margin-bottom: 24px;">
    <a href="{{ route('expenses.index') }}" class="btn btn-sm" style="margin-right: 12px;">
        ← Back
    </a>
    <h1 class="page-title" style="margin-bottom: 0;">Expense Details</h1>
</div>

<div class="row">
    <div class="col" style="flex: 1;">
        <div class="card">
            <div class="card-header">Overview</div>
            <div class="card-body">
                <div style="background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(99,102,241,0.02) 100%); border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;">
                        ${{ number_format($expense->amount, 2) }}
                    </div>
                    <div style="font-size: 1.1rem; color: var(--text-color); font-weight: 500;">
                        {{ $expense->description }}
                    </div>
                    <div style="margin-top: 8px; color: var(--light-text);">
                        {{ date('F d, Y', strtotime($expense->expense_date)) }}
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; margin-bottom: 16px;">
                        <div style="min-width: 110px; font-weight: 500; color: var(--light-text);">Created By</div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; background-color: {{ '#' . substr(md5($expense->creator->id), 0, 6) }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                {{ strtoupper(substr($expense->creator->name, 0, 1)) }}
                            </div>
                            <span style="font-weight: 500;">{{ $expense->creator->name }}</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 16px;">
                        <div style="min-width: 110px; font-weight: 500; color: var(--light-text);">Split Method</div>
                        <span class="badge {{ $expense->split_method === 'equal' ? 'badge-equal' : 'badge-custom' }}" style="font-size: 0.85rem;">
                            {{ ucfirst($expense->split_method) }}
                        </span>
                    </div>
                    
                    <div style="display: flex; align-items: center;">
                        <div style="min-width: 110px; font-weight: 500; color: var(--light-text);">Date Added</div>
                        <div>{{ date('F d, Y', strtotime($expense->created_at)) }}</div>
                    </div>
                </div>
                
                @if($expense->created_by == Auth::id())
                    <div style="display: flex; gap: 10px; margin-top: 24px;">
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn">Edit Expense</a>
                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this expense?')">Delete Expense</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col" style="flex: 1;">
        <div class="card">
            <div class="card-header">Participants</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Share</th>
                                <th>Paid</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense->users as $user)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 32px; height: 32px; background-color: {{ '#' . substr(md5($user->id), 0, 6) }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span style="font-weight: 500;">{{ $user->name }}</span>
                                            @if($user->id == $expense->created_by)
                                                <span style="background-color: rgba(34, 197, 94, 0.1); color: #16a34a; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; font-weight: 600;">PAID</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>${{ number_format($user->pivot->share, 2) }}</td>
                                    <td>${{ number_format($user->pivot->amount_paid, 2) }}</td>
                                    <td>
                                        @php
                                            $balance = $user->pivot->amount_paid - $user->pivot->share;
                                        @endphp
                                        <span class="{{ $balance > 0 ? 'positive' : ($balance < 0 ? 'negative' : '') }}">
                                            ${{ number_format($balance, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 24px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: var(--primary-color);">Split Visualization</h3>
                    <div style="height: 20px; background-color: #f1f5f9; border-radius: 10px; overflow: hidden; position: relative; margin-bottom: 8px;">
                        @php
                            $currentOffset = 0;
                            $totalAmount = $expense->amount;
                        @endphp
                        
                        @foreach($expense->users as $user)
                            @php
                                $percentage = ($user->pivot->share / $totalAmount) * 100;
                                $color = '#' . substr(md5($user->id), 0, 6);
                            @endphp
                            
                            <div style="position: absolute; left: {{ $currentOffset }}%; width: {{ $percentage }}%; height: 100%; background-color: {{ $color }}; opacity: 0.7;"></div>
                            
                            @php
                                $currentOffset += $percentage;
                            @endphp
                        @endforeach
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 12px;">
                        @foreach($expense->users as $user)
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="width: 12px; height: 12px; border-radius: 3px; background-color: {{ '#' . substr(md5($user->id), 0, 6) }};"></div>
                                <span style="font-size: 0.85rem;">{{ $user->name }} ({{ number_format(($user->pivot->share / $totalAmount) * 100, 1) }}%)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
