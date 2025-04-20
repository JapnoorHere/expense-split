@extends('layouts.app')

@section('content')
<div style="display: flex; align-items: center; margin-bottom: 24px;">
    <a href="{{ route('expenses.index') }}" class="btn btn-sm" style="margin-right: 12px;">
        ← Back
    </a>
    <h1 class="page-title" style="margin-bottom: 0;">Add New Expense</h1>
</div>

<div class="row">
    <div class="col" style="flex: 3;">
        <div class="card">
            <div class="card-header">Expense Details</div>
            <div class="card-body">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" placeholder="Enter expense description, e.g. Dinner at Restaurant" required>
                        @error('description')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <div style="position: relative;">
                            <div style="position: absolute; left: 12px; top: 12px; color: var(--text-color);">$</div>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" style="padding-left: 28px;" required>
                        </div>
                        @error('amount')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="expense_date">Date</label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        @error('expense_date')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Split Method</label>
                        <div class="split-method-selector">
                            <label class="split-method-option">
                                <input type="radio" name="split_method" value="equal" checked onclick="toggleSplitMethod('equal')">
                                <div class="split-method-content">
                                    <div class="split-icon">🔄</div>
                                    <div>
                                        <h3>Equal Split</h3>
                                        <p>Split the expense equally among all participants</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="split-method-option">
                                <input type="radio" name="split_method" value="custom" onclick="toggleSplitMethod('custom')">
                                <div class="split-method-content">
                                    <div class="split-icon">📊</div>
                                    <div>
                                        <h3>Custom Split</h3>
                                        <p>Specify custom percentage for each participant</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('split_method')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Participants</label>
                        <div class="participants-container">
                            @foreach($users as $user)
                                <div class="participant-card">
                                    <label class="participant-checkbox">
                                        <input type="checkbox" name="users[]" value="{{ $user->id }}" 
                                            {{ $user->id == Auth::id() ? 'checked disabled' : '' }} 
                                            onclick="updatePercentageFields()">
                                        <input type="hidden" name="users[]" value="{{ Auth::id() }}" {{ $user->id == Auth::id() ? '' : 'disabled' }}>
                                        
                                        <div class="participant-info">
                                            <div class="participant-avatar" style="background-color: {{ '#' . substr(md5($user->id), 0, 6) }};">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $user->name }}</span>
                                            @if($user->id == Auth::id())
                                                <span class="user-badge">You</span>
                                            @endif
                                        </div>
                                    </label>
                                    
                                    <div class="percentage-input" style="display: none;">
                                        <div class="percentage-slider-container">
                                            <input type="range" min="0" max="100" value="0" class="percentage-slider" id="slider_{{ $user->id }}" oninput="updatePercentageValue({{ $user->id }})">
                                            <div class="percentage-value-container">
                                                <input type="number" name="percentage_{{ $user->id }}" id="percentage_{{ $user->id }}" min="0" max="100" value="0" class="percentage-value" oninput="updateSliderValue({{ $user->id }})">
                                                <span class="percentage-symbol">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('users')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn">Save Expense</button>
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
                    <div class="preview-amount">$0.00</div>
                    <div class="preview-description">Expense Description</div>
                    <div class="preview-date">{{ date('F d, Y') }}</div>
                </div>
                
                <div id="participants-preview" style="margin-top: 30px;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: var(--primary-color);">Participants</h3>
                    <div id="participants-list" style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="preview-participant" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border-radius: 8px; background-color: rgba(99,102,241,0.05);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background-color: {{ '#' . substr(md5(Auth::id()), 0, 6) }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span>{{ Auth::user()->name }} (You)</span>
                            </div>
                            <div id="preview-share-you">$0.00</div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: var(--primary-color);">Tips</h3>
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                        <li style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                            <div style="color: var(--primary-color); font-weight: bold;">1.</div>
                            <div>Enter the full amount you paid, even if you're splitting it with others.</div>
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                            <div style="color: var(--primary-color); font-weight: bold;">2.</div>
                            <div>Select all participants who were involved in this expense.</div>
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 10px;">
                            <div style="color: var(--primary-color); font-weight: bold;">3.</div>
                            <div>Choose equal split for simplicity or custom split for more control over who pays what.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.split-method-selector {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.split-method-option {
    flex: 1;
    cursor: pointer;
}

.split-method-option input[type="radio"] {
    display: none;
}

.split-method-content {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.split-method-option input[type="radio"]:checked + .split-method-content {
    border-color: var(--primary-color);
    background-color: rgba(99, 102, 241, 0.05);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
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

.participant-checkbox {
    display: block;
    cursor: pointer;
}

.participant-checkbox input[type="checkbox"] {
    display: none;
}

.participant-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    transition: all 0.3s ease;
}

.participant-checkbox input[type="checkbox"]:checked + .participant-info {
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

.percentage-input {
    padding: 0 15px 15px;
}

.percentage-slider-container {
    display: flex;
    align-items: center;
    gap: 15px;
}

.percentage-slider {
    flex: 1;
    height: 8px;
    -webkit-appearance: none;
    appearance: none;
    background: #e2e8f0;
    outline: none;
    border-radius: 4px;
}

.percentage-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--primary-color);
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.percentage-value-container {
    display: flex;
    align-items: center;
    min-width: 80px;
}

.percentage-value {
    width: 45px;
    text-align: right;
    padding: 4px;
    font-size: 0.9rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
}

.percentage-symbol {
    margin-left: 4px;
    color: var(--light-text);
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
    function toggleSplitMethod(method) {
        const percentageInputs = document.querySelectorAll('.percentage-input');
        if (method === 'custom') {
            percentageInputs.forEach(input => {
                input.style.display = 'block';
            });
            updatePercentageFields();
        } else {
            percentageInputs.forEach(input => {
                input.style.display = 'none';
            });
            updatePercentageFields();
        }
    }
    
    function updatePercentageFields() {
        // Only relevant for custom split
        if (document.querySelector('input[name="split_method"]:checked').value !== 'custom') {
            updatePreview();
            return;
        }
        
        const checkboxes = document.querySelectorAll('input[name="users[]"]:checked');
        const count = checkboxes.length;
        
        if (count > 0) {
            const equalPercentage = Math.floor(100 / count);
            let remainder = 100 - (equalPercentage * count);
            
            checkboxes.forEach((checkbox, index) => {
                const userId = checkbox.value;
                const percentInput = document.querySelector(`input[name="percentage_${userId}"]`);
                const sliderInput = document.querySelector(`#slider_${userId}`);
                
                if (percentInput && sliderInput) {
                    let percentage = equalPercentage;
                    if (index === 0) {
                        percentage += remainder;
                    }
                    percentInput.value = percentage;
                    sliderInput.value = percentage;
                }
            });
        }
        
        updatePreview();
    }
    
    function updatePercentageValue(userId) {
        const slider = document.getElementById(`slider_${userId}`);
        const input = document.getElementById(`percentage_${userId}`);
        input.value = slider.value;
        updatePreview();
    }
    
    function updateSliderValue(userId) {
        const slider = document.getElementById(`slider_${userId}`);
        const input = document.getElementById(`percentage_${userId}`);
        
        if (input.value > 100) {
            input.value = 100;
        } else if (input.value < 0) {
            input.value = 0;
        }
        
        slider.value = input.value;
        updatePreview();
    }
    
    function updatePreview() {
        // Update expense details
        const amount = document.getElementById('amount').value || 0;
        const description = document.getElementById('description').value || 'Expense Description';
        const date = document.getElementById('expense_date').value;
        
        document.querySelector('.preview-amount').textContent = `$${parseFloat(amount).toFixed(2)}`;
        document.querySelector('.preview-description').textContent = description;
        
        if (date) {
            const formattedDate = new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.querySelector('.preview-date').textContent = formattedDate;
        }
        
        // Update participants preview
        const participantsList = document.getElementById('participants-list');
        participantsList.innerHTML = '';
        
        const splitMethod = document.querySelector('input[name="split_method"]:checked').value;
        const checkboxes = document.querySelectorAll('input[name="users[]"]:checked');
        const userCount = checkboxes.length;
        
        if (userCount > 0) {
            let yourShare = 0;
            
            checkboxes.forEach(checkbox => {
                const userId = checkbox.value;
                const participantName = checkbox.parentElement.querySelector('span').textContent;
                const avatarBg = checkbox.parentElement.querySelector('.participant-avatar').style.backgroundColor;
                const avatarText = checkbox.parentElement.querySelector('.participant-avatar').textContent;
                const isYou = participantName.includes('You');
                
                let share = 0;
                
                if (splitMethod === 'equal') {
                    share = amount / userCount;
                } else {
                    const percentInput = document.querySelector(`input[name="percentage_${userId}"]`);
                    if (percentInput) {
                        share = (percentInput.value / 100) * amount;
                    }
                }
                
                if (isYou) {
                    yourShare = share;
                    document.getElementById('preview-share-you').textContent = `$${share.toFixed(2)}`;
                } else {
                    const participantElement = document.createElement('div');
                    participantElement.className = 'preview-participant';
                    participantElement.style.display = 'flex';
                    participantElement.style.alignItems = 'center';
                    participantElement.style.justifyContent = 'space-between';
                    participantElement.style.padding = '12px';
                    participantElement.style.borderRadius = '8px';
                    participantElement.style.backgroundColor = 'rgba(99,102,241,0.05)';
                    
                    participantElement.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; background-color: ${avatarBg}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                ${avatarText}
                            </div>
                            <span>${participantName}</span>
                        </div>
                        <div>$${share.toFixed(2)}</div>
                    `;
                    
                    participantsList.appendChild(participantElement);
                }
            });
        }
    }
    
    // Initial setup
    document.addEventListener('DOMContentLoaded', function() {
        toggleSplitMethod('equal');
        
        // Update preview when amount or description changes
        document.getElementById('amount').addEventListener('input', updatePreview);
        document.getElementById('description').addEventListener('input', updatePreview);
        document.getElementById('expense_date').addEventListener('input', updatePreview);
        
        updatePreview();
    });
</script>
@endsection
