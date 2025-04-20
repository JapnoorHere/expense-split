<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Expense Splitter') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <style>
            /* Modern Glass UI CSS */
            :root {
                --primary-color: #6366f1;
                --primary-hover: #4f46e5;
                --secondary-color: #f97316;
                --text-color: #334155;
                --light-text: #94a3b8;
                --bg-color: #f8fafc;
                --card-bg: rgba(255, 255, 255, 0.9);
                --card-border: rgba(255, 255, 255, 0.5);
                --card-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Poppins', sans-serif;
                line-height: 1.6;
                background-color: var(--bg-color);
                color: var(--text-color);
                background-image: 
                    radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 20%),
                    radial-gradient(circle at 80% 80%, rgba(249, 115, 22, 0.1) 0%, transparent 20%);
                background-attachment: fixed;
                min-height: 100vh;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            /* Navbar Styles */
            .navbar {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--card-border);
                position: sticky;
                top: 0;
                z-index: 1000;
                padding: 15px 0;
            }
            
            .navbar-inner {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .navbar-brand {
                color: var(--primary-color);
                font-size: 1.5rem;
                font-weight: 700;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .navbar-brand:before {
                content: "💰";
                font-size: 1.3rem;
            }
            
            .navbar-nav {
                display: flex;
                list-style: none;
                gap: 5px;
            }
            
            .nav-item {
                position: relative;
            }
            
            .nav-link {
                color: var(--text-color);
                text-decoration: none;
                padding: 8px 16px;
                border-radius: 8px;
                transition: all 0.3s ease;
                font-weight: 500;
                display: block;
            }
            
            .nav-link:hover, .active {
                background: rgba(99, 102, 241, 0.1);
                color: var(--primary-color);
            }
            
            button.nav-link {
                background: none;
                border: none;
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
                font-size: 1rem;
                cursor: pointer;
                width: 100%;
                text-align: left;
            }
            
            /* Card Styles */
            .card {
                background: var(--card-bg);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-radius: 16px;
                box-shadow: var(--card-shadow);
                border: 1px solid var(--card-border);
                margin-bottom: 24px;
                overflow: hidden;
            }
            
            .card-header {
                font-size: 1.2rem;
                font-weight: 600;
                padding: 20px;
                background: rgba(255, 255, 255, 0.5);
                border-bottom: 1px solid var(--card-border);
                color: var(--primary-color);
            }
            
            .card-body {
                padding: 20px;
            }
            
            /* Button Styles */
            .btn {
                display: inline-block;
                background-color: var(--primary-color);
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 500;
                transition: all 0.3s ease;
                box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
            }
            
            .btn:hover {
                background-color: var(--primary-hover);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
            }
            
            .btn-secondary {
                background-color: var(--secondary-color);
                box-shadow: 0 2px 10px rgba(249, 115, 22, 0.3);
            }
            
            .btn-secondary:hover {
                background-color: #ea580c;
                box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
            }
            
            .btn-danger {
                background-color: #ef4444;
                box-shadow: 0 2px 10px rgba(239, 68, 68, 0.3);
            }
            
            .btn-danger:hover {
                background-color: #dc2626;
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
            }
            
            .btn-sm {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            
            /* Form Styles */
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: var(--text-color);
            }
            
            .form-control {
                width: 100%;
                padding: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                font-family: 'Poppins', sans-serif;
                font-size: 0.95rem;
                transition: all 0.3s ease;
            }
            
            .form-control:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            }
            
            /* Table Styles */
            .table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                margin-bottom: 16px;
            }
            
            .table th, .table td {
                padding: 14px;
                text-align: left;
            }
            
            .table th {
                font-weight: 600;
                background-color: rgba(99, 102, 241, 0.05);
                color: var(--primary-color);
                position: relative;
            }
            
            .table th:first-child {
                border-radius: 8px 0 0 8px;
            }
            
            .table th:last-child {
                border-radius: 0 8px 8px 0;
            }
            
            .table tr {
                transition: all 0.3s ease;
            }
            
            .table tbody tr:hover {
                background-color: rgba(99, 102, 241, 0.05);
            }
            
            .table td {
                border-bottom: 1px solid #e2e8f0;
            }
            
            .table tbody tr:last-child td {
                border-bottom: none;
            }
            
            /* Alert Styles */
            .alert {
                padding: 16px;
                margin-bottom: 20px;
                border-radius: 8px;
                font-weight: 500;
            }
            
            .alert-success {
                background-color: rgba(34, 197, 94, 0.1);
                color: #16a34a;
                border: 1px solid rgba(34, 197, 94, 0.2);
            }
            
            .alert-danger {
                background-color: rgba(239, 68, 68, 0.1);
                color: #dc2626;
                border: 1px solid rgba(239, 68, 68, 0.2);
            }
            
            /* Layout Helpers */
            .row {
                display: flex;
                flex-wrap: wrap;
                margin: 0 -12px;
            }
            
            .col {
                flex: 1;
                padding: 0 12px;
                min-width: 0;
            }
            
            @media (max-width: 768px) {
                .row {
                    flex-direction: column;
                }
                
                .col {
                    margin-bottom: 20px;
                }
                
                .navbar-inner {
                    flex-direction: column;
                    gap: 15px;
                }
                
                .navbar-nav {
                    flex-wrap: wrap;
                    justify-content: center;
                }
            }
            
            /* Dashboard Stats */
            .stat-card {
                display: flex;
                align-items: center;
                padding: 16px;
                background: linear-gradient(135deg, rgba(255,255,255,0.7) 0%, rgba(255,255,255,0.3) 100%);
                border-radius: 12px;
                margin-bottom: 16px;
                border: 1px solid var(--card-border);
                transition: transform 0.3s ease;
            }
            
            .stat-card:hover {
                transform: translateY(-5px);
            }
            
            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                margin-right: 16px;
                background: rgba(255, 255, 255, 0.9);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }
            
            .stat-content h3 {
                font-size: 0.85rem;
                color: var(--light-text);
                margin-bottom: 4px;
            }
            
            .stat-content p {
                font-size: 1.5rem;
                font-weight: 600;
                color: var(--text-color);
            }
            
            .positive {
                color: #16a34a;
            }
            
            .negative {
                color: #dc2626;
            }
            
            /* Expense badges */
            .badge {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
            }
            
            .badge-equal {
                background-color: rgba(99, 102, 241, 0.1);
                color: var(--primary-color);
            }
            
            .badge-custom {
                background-color: rgba(249, 115, 22, 0.1);
                color: var(--secondary-color);
            }
            
            /* Helper classes */
            .text-center {
                text-align: center;
            }
            
            .mt-4 {
                margin-top: 16px;
            }
            
            .mb-4 {
                margin-bottom: 16px;
            }
            
            .page-title {
                font-size: 1.6rem;
                font-weight: 700;
                margin-bottom: 24px;
                color: var(--primary-color);
            }
        </style>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body>
        <nav class="navbar">
            <div class="container">
                <div class="navbar-inner">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Expense Splitter
                    </a>
                    <div>
                        <ul class="navbar-nav">
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">Expenses</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('reports') ? 'active' : '' }}" href="{{ route('reports') }}">Reports</a>
                                </li>
                                <li class="nav-item">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="nav-link">
                                            {{ __('Logout') }}
                                        </button>
                                    </form>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main class="container" style="padding-top: 30px; padding-bottom: 30px;">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
