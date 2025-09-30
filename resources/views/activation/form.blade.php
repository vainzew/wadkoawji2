<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Aktivasi Aplikasi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Core UI CSS -->
    <link href="{{ asset('coreui/dist/css/coreui.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .activation-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .activation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            animation: slideUp 0.6s ease-out;
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

        .activation-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .activation-icon {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 20px;
        }

        .activation-title {
            color: #495057;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .activation-subtitle {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
            text-align: center;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-activate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .btn-activate:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .activation-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #6c757d;
            font-family: 'Courier New', monospace;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .invalid-feedback {
            display: block;
            font-size: 14px;
            color: #dc3545;
            margin-top: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="activation-container">
        <div class="activation-card">
            <!-- Header -->
            <div class="activation-header">
                <div class="activation-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="activation-title">Aktivasi Aplikasi</div>
                <div class="activation-subtitle">Masukkan kode aktivasi untuk menggunakan aplikasi</div>
            </div>

            <!-- System Info -->
            <div class="activation-info">
                <div class="info-item">
                    <span class="info-label">Hardware ID:</span>
                    <span class="info-value">{{ generateHardwareId() }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Domain:</span>
                    <span class="info-value">{{ request()->getHost() }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Server:</span>
                    <span class="info-value">{{ php_uname('n') }}</span>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Activation Form -->
            <form action="{{ route('activation.activate') }}" method="post" id="activationForm">
                @csrf

                <div class="mb-3">
                    <label for="activation_code" class="form-label">Kode Aktivasi (16 karakter)</label>
                    <input type="text"
                           name="activation_code"
                           id="activation_code"
                           class="form-control @error('activation_code') is-invalid @enderror"
                           placeholder="ABCD1234EFGH5678"
                           maxlength="16"
                           value="{{ old('activation_code') }}"
                           required
                           autofocus>
                    @error('activation_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-activate" id="submitBtn">
                    <i class="fas fa-key me-2"></i>
                    <span id="btnText">Aktivasi Sekarang</span>
                    <span id="btnLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Mengaktivasi...
                    </span>
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Hubungi administrator untuk mendapatkan kode aktivasi
                </small>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('coreui/dist/js/coreui.bundle.min.js') }}"></script>

    <script>
        // Format activation code input - only alphanumeric, uppercase
        $('#activation_code').on('input', function() {
            let value = $(this).val().replace(/[^A-Za-z0-9]/g, '').toUpperCase();

            if (value.length > 16) {
                value = value.substring(0, 16);
            }

            $(this).val(value);
            $(this).removeClass('is-invalid');
        });

        // Form submission
        $('#activationForm').on('submit', function() {
            $('#btnText').hide();
            $('#btnLoading').show();
            $('#submitBtn').prop('disabled', true);
        });

        // Auto-focus and select
        $('#activation_code').focus().select();

        // Paste handling
        $('#activation_code').on('paste', function(e) {
            setTimeout(() => {
                let value = $(this).val().replace(/[^A-Za-z0-9]/g, '').toUpperCase();

                if (value.length > 16) {
                    value = value.substring(0, 16);
                }

                $(this).val(value);
            }, 10);
        });
    </script>
</body>
</html>