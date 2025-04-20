@extends('layouts.app')

@section('content')
<div style="display: flex; align-items: center; margin-bottom: 24px;">
    <a href="{{ route('expenses.index') }}" class="btn btn-sm" style="margin-right: 12px;">
        ← Back
    </a>
    <h1 class="page-title" style="margin-bottom: 0;">Edit Expense</h1>
</div>

<div class="row">
    <div class="col" style="flex: 3;">
        <div class="card">
            <div class="card-header">Expense Details</div>
            <div class="card-body">
                <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $expense->description) }}" placeholder="Enter expense description" required>
                        @error('description')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <div style="position: relative;">
                            <div style="position: absolute; left: 12px; top: 12px; color: var(--text-color);">$</div>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $expense->amount) }}" style="padding-left: 28px;" required>
                        </div>
                        @error('amount')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="expense_date">Date</label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date) }}" required>
                        @error('expense_date')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Split Method</label>
                        <div class="split-method-view">
                            <div class="split-method-content">
                                <div class="split-icon">
                                    {{ $expense->split_method === 'equal' ? '🔄' : '📊' }}
                                </div>
                                <div>
                                    <h3>{{ ucfirst($expense->split_method) }} Split</h3>
                                    <p>
                                        @if($expense->split_method === 'equal')
                                            The expense is split equally among all participants
                                        @else
                                            The expense is split using custom percentages
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <p class="text-muted" style="margin-top: 10px; color: var(--light-text);">Note: Split method cannot be changed. Create a new expense to use a different split method.</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Participants</label>
                        <div class="participants-container">
                            @foreach($expense->users as $user)
                                <div class="participant-card">
                                    <div class="participant-info">
                                        <div class="participant-avatar" style="background-color: {{ '#' . substr(md5($user->id), 0, 6) }};">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                        @if($user->id == Auth::id())
                                            <span class="user-badge">You</span>
                                        @endif
                                        
                                        <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
                                            <span style="color: var(--light-text);">Share:</span>
                                            <span style="font-weight: 500;">${{ number_format($user->pivot->share, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted" style="margin-top: 10px; color: var(--light-text);">Note: Participants cannot be changed. Create a new expense to add or remove participants.</p>
                    </div>
                    
                    <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;">
                        <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col" style="flex: 2;">
        <div class="card">
            <div class="card-header">Preview</div>
            <div class="card-body">
                <div id="expense-preview" style="padding: 20px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(99,102,241,0.02) 100%);">
                    <div class="preview-amount">${{ number_format($expense->amount, 2) }}</div>
                    <div class="preview-description">{{ $expense->description }}</div>
                    <div class="preview-date">{{ date('F d, Y', strtotime($expense->expense_date)) }}</div>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: var(--primary-color);">Split Visualization</h3>
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
                
                <div style="margin-top: 30px;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: var(--primary-color);">Note</h3>
                    <div style="padding: 15px; border-radius: 8px; background-color: rgba(249, 115, 22, 0.05); border: 1px solid rgba(249, 115, 22, 0.1);">
                        <p style="margin: 0; color: var(--secondary-color);">
                            You can only update the description, amount, and date of this expense. To change participants or split method, please create a new expense instead.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.split-method-view {
    margin-top: 10px;
}

.split-method-content {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background-color: rgba(99, 102, 241, 0.05);
}

.split-icon {
    font-size: 1.5rem;
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(99, 102, 241, 0.1);
    border-radius: 8px;
}

.split-method-content h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 5px 0;
    color: var(--text-color);
}

.split-method-content p {
    font-size: 0.85rem;
    margin: 0;
    color: var(--light-text);
}

.participants-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 15px;
}

.participant-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

.participant-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background-color: rgba(99, 102, 241, 0.05);
}

.participant-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.user-badge {
    background-color: rgba(99, 102, 241, 0.1);
    color: var(--primary-color);
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 600;
    margin-left: 5px;
}

.preview-amount {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 8px;
    text-align: center;
}

.preview-description {
    font-size: 1.1rem;
    color: var(--text-color);
    font-weight: 500;
    margin-bottom: 8px;
    text-align: center;
}

.preview-date {
    color: var(--light-text);
    text-align: center;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const descriptionInput = document.getElementById('description');
        const dateInput = document.getElementById('expense_date');
        
        function updatePreview() {
            const previewAmount = document.querySelector('.preview-amount');
            const previewDescription = document.querySelector('.preview-description');
            const previewDate = document.querySelector('.preview-date');
            
            previewAmount.textContent = `$${parseFloat(amountInput.value).toFixed(2)}`;
            previewDescription.textContent = descriptionInput.value;
            
            const date = new Date(dateInput.value);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            previewDate.textContent = date.toLocaleDateString('en-US', options);
        }
        
        amountInput.addEventListener('input', updatePreview);
        descriptionInput.addEventListener('input', updatePreview);
        dateInput.addEventListener('input', updatePreview);
    });
</script>
@endsection
