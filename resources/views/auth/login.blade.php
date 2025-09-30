<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{{ $setting->nama_perusahaan }} | Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" href="{{ url($setting->path_logo) }}" type="image/png">
    
    <!-- Core UI CSS -->
    <link href="{{ asset('coreui/dist/css/coreui.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa; /* Putih soft */
            min-height: 100vh;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            animation: slideUp 0.6s ease-out;
            border: 1px solid #e0e0e0; /* Border soft hitam */
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-container img {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .welcome-text {
            color: #495057;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .subtitle-text {
            color: #6c757d;
            font-size: 14px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0);
        }
        
        .btn-login {
            background: #605CA8; /* Warna solid #605CA8 */
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            
            box-shadow: 0 8px 20px rgba(96, 92, 168, 0.3); /* Shadow dengan warna yang sesuai */
            color: white;
            background: #a39fec;
        }
        
        .form-check {
            margin: 20px 0;
        }
        
        .invalid-feedback {
            display: block;
            font-size: 14px;
            color: #dc3545;
            margin-top: 5px;
        }
        
        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #6c757d;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 12px;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-container">
                <img src="{{ url($setting->path_logo) }}" alt="{{ $setting->nama_perusahaan }}">
                <div class="welcome-text">Welcome Back!</div>
                <div class="subtitle-text">Sign in to continue to {{ $setting->nama_perusahaan }}</div>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="post" class="form-login" id="loginForm">
                @csrf
                
                <!-- Username -->
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                           placeholder="Username" required value="{{ old('username') }}" autofocus>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Password" required id="passwordField">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()" style="border-radius: 0 12px 12px 0;">
                        <i class="fas fa-eye" id="passwordToggle"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In
                </button>
            </form>
            
            <div class="footer-text">
                &copy; {{ date('Y') }} {{ $setting->nama_perusahaan }}. All rights reserved.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('coreui/dist/js/coreui.bundle.min.js') }}"></script>
    
    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordField = document.getElementById('passwordField');
            const passwordToggle = document.getElementById('passwordToggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }
        
        // Form submission with loading overlay
        $('#loginForm').on('submit', function() {
            $('#loadingOverlay').css('display', 'flex');
        });
        
        // Auto-hide loading on page load
        $(document).ready(function() {
            $('#loadingOverlay').hide();
        });
        
        // Form validation enhancement
        $('.form-control').on('input', function() {
            $(this).removeClass('is-invalid');
        });
    </script>
</body>
</html>