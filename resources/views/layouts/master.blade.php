<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{{ $setting->nama_perusahaan }} | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" href="{{ url($setting->path_logo) }}" type="image/png">
    
    <!-- Core UI CSS -->
    <link href="{{ asset('coreui/dist/css/coreui.min.css') }}" rel="stylesheet">
    <!-- CoreUI Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@coreui/icons@2.1.0/css/all.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables with Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- DatePicker -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <style>
        /* Core UI Customizations */
        .sidebar {
            --cui-sidebar-bg: #1e293b;
            --cui-sidebar-color: #94a3b8;
            --cui-sidebar-nav-link-hover-bg: rgba(148, 163, 184, 0.1);
        }
        
        .sidebar-nav-link.active {
            background-color: #3b82f6 !important;
            color: white !important;
        }
        
        /* Full width content optimization - sesuai preference user */
        .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        
        .main {
            padding-left: 0;
            padding-right: 0;
        }
        
        /* Stock Notifications - maintain existing functionality */
        .notifications-container {
            position: fixed;
            top: 70px;
            right: 20px;
            max-width: 350px;
            z-index: 1060;
        }

        .stock-notification {
            background: #fff;
            border-left: 4px solid #f59e0b;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 10px;
            display: none;
            animation: slideIn 0.5s ease-in-out;
        }

        .stock-notification.negative-stock {
            border-left-color: #ef4444;
        }

        .stock-notification-content {
            font-size: 13px;
            color: #374151;
            margin-right: 20px;
        }

        .stock-notification .close-notification {
            position: absolute;
            right: 8px;
            top: 8px;
            cursor: pointer;
            color: #6b7280;
            font-size: 14px;
        }

        .stock-notification .product-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 5px;
        }

        .stock-notification .stock-count {
            font-size: 12px;
            color: #6b7280;
        }

        .stock-notification .stock-warning {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            margin-top: 5px;
        }

        .stock-notification.negative-stock .stock-warning {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .stock-notification .stock-warning {
            background-color: #fffbeb;
            color: #d97706;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Breadcrumb styling */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item a {
            color: #6b7280;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #374151;
        }
        
        /* Content header */
        .content-header {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
        }
        
        /* Minimize white space - user preference */
        .card {
            margin-bottom: 1rem;
        }
        
        .row {
            margin-left: -8px;
            margin-right: -8px;
        }
        
        .col, .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, 
        .col-7, .col-8, .col-9, .col-10, .col-11, .col-12,
        .col-sm, .col-md, .col-lg, .col-xl {
            padding-left: 8px;
            padding-right: 8px;
        }
    </style>
    
    @stack('css')
</head>

<body class="c-app">
    <div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
        @includeIf('layouts.coreui.sidebar')
    </div>

    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
        <!-- Header -->
        <header class="header header-sticky mb-4">
            @includeIf('layouts.coreui.header')
        </header>
        
        <div class="body flex-grow-1 px-3">
            <div class="container-fluid">
                <!-- Content Header -->
                <div class="content-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">@yield('title')</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @section('breadcrumb')
                                        <li class="breadcrumb-item">
                                            <a href="{{ url('/dashboard') }}">
                                                <i class="fa fa-dashboard"></i> Home
                                            </a>
                                        </li>
                                    @show
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                @yield('content')
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="footer">
            @includeIf('layouts.coreui.footer')
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('coreui/dist/js/coreui.bundle.min.js') }}"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- DatePicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    
    <!-- Validator -->
    <script src="{{ asset('js/validator.min.js') }}"></script>

    <script>
        // Maintain existing stock notification functionality
        function showStockNotifications(lowStockItems) {
            // Hapus container notifikasi yang ada jika sudah ada
            if ($('.notifications-container').length === 0) {
                $('body').append('<div class="notifications-container"></div>');
            }
            
            lowStockItems.forEach((item, index) => {
                const isNegative = item.stok < 0;
                const notificationHtml = `
                    <div class="stock-notification ${isNegative ? 'negative-stock' : ''}" style="animation-delay: ${index * 0.2}s">
                        <span class="close-notification">&times;</span>
                        <div class="stock-notification-content">
                            <div class="product-name">${item.nama_produk}</div>
                            <div class="stock-count">Sisa stok: ${item.stok} unit</div>
                            <div class="stock-warning">
                                ${isNegative ? 'Stok Minus!' : 'Stok Menipis!'}
                            </div>
                        </div>
                    </div>
                `;
                
                $('.notifications-container').append(notificationHtml);
                
                // Tampilkan notifikasi dengan delay berurutan
                setTimeout(() => {
                    $('.notifications-container .stock-notification').eq(index).fadeIn();
                }, index * 200);
            });
            
            // Event untuk menutup notifikasi individual
            $('.close-notification').click(function() {
                $(this).parent().fadeOut(function() {
                    $(this).remove();
                    // Hapus container jika tidak ada notifikasi lagi
                    if ($('.stock-notification').length === 0) {
                        $('.notifications-container').remove();
                    }
                });
            });
            
            // Otomatis hilangkan notifikasi setelah beberapa waktu
            lowStockItems.forEach((item, index) => {
                setTimeout(() => {
                    const notification = $('.notifications-container .stock-notification').eq(index);
                    if (notification.length) {
                        notification.fadeOut(function() {
                            $(this).remove();
                            // Hapus container jika tidak ada notifikasi lagi
                            if ($('.stock-notification').length === 0) {
                                $('.notifications-container').remove();
                            }
                        });
                    }
                }, (15000 + (index * 2000))); // Notifikasi akan hilang setelah 15 detik + 2 detik per item
            });
        }

        // Fungsi untuk memeriksa stok
        function checkLowStock() {
            $.get('{{ route('produk.check_stock') }}')
                .done(response => {
                    if (response.length > 0) {
                        showStockNotifications(response);
                    }
                })
                .fail(errors => {
                    console.error('Gagal memeriksa stok:', errors);
                });
        }

        // Jalankan pengecekan stok setiap kali halaman dimuat
        $(document).ready(function() {
            checkLowStock();
            
            // Periksa stok setiap 10 menit
            setInterval(checkLowStock, 10 * 60 * 1000);
        });
    </script>
    
    @stack('scripts')
    
    <div id="temp-barcode" style="position: absolute; top: -9999px; left: -9999px;"></div>
</body>
</html>