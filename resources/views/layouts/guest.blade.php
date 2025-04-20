<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Glass UI Styles -->
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
                --error-color: #dc2626;
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
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px 20px;
                margin: 0;
            }
            
            .auth-card {
                width: 100%;
                max-width: 480px;
                background: var(--card-bg);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-radius: 16px;
                box-shadow: var(--card-shadow);
                border: 1px solid var(--card-border);
                overflow: hidden;
            }
            
            .auth-card-header {
                padding: 30px;
                text-align: center;
                background: rgba(255, 255, 255, 0.5);
                border-bottom: 1px solid var(--card-border);
            }
            
            .auth-card-header h2 {
                color: var(--primary-color);
                font-size: 1.8rem;
                font-weight: 700;
                margin-bottom: 8px;
            }
            
            .auth-card-header p {
                color: var(--light-text);
                font-size: 1rem;
            }
            
            form {
                padding: 30px;
            }
            
            .form-group {
                margin-bottom: 20px;
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
            
            .error-message {
                color: var(--error-color);
                font-size: 0.875rem;
                margin-top: 5px;
            }
            
            .remember-me {
                margin-bottom: 20px;
            }
            
            .remember-label {
                display: flex;
                align-items: center;
                cursor: pointer;
            }
            
            .remember-checkbox {
                margin-right: 8px;
            }
            
            .form-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            
            .auth-link {
                color: var(--primary-color);
                text-decoration: none;
                font-size: 0.9rem;
                transition: all 0.3s ease;
            }
            
            .auth-link:hover {
                text-decoration: underline;
            }
            
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
            
            .register-link {
                text-align: center;
                color: var(--light-text);
                font-size: 0.9rem;
            }
            
            .register-link a {
                color: var(--primary-color);
                text-decoration: none;
                font-weight: 500;
            }
            
            .register-link a:hover {
                text-decoration: underline;
            }
            
            .mb-4 {
                margin-bottom: 1rem;
            }
            
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
            
            @media (max-width: 500px) {
                .form-actions {
                    flex-direction: column;
                    gap: 15px;
                }
                
                .form-actions .btn {
                    width: 100%;
                    text-align: center;
                }
                
                .auth-link {
                    display: block;
                    text-align: center;
                }
            }
        </style>
    </head>
    <body>
        <div>
            {{ $slot }}
        </div>
    </body>
</html>
