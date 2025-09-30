<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $setting->nama_perusahaan }} | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="icon" href="{{ url($setting->path_logo) }}" type="image/png">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @stack('css')
    <style>
    .notifications-container {
        position: fixed;
        top: 70px;
        right: 20px;
        max-width: 350px;
        z-index: 1000;
    }

    .stock-notification {
        background: #fff;
        border-left: 4px solid #ff9800;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        padding: 12px 20px;
        border-radius: 4px;
        margin-bottom: 10px;
        display: none;
        animation: slideIn 0.5s ease-in-out;
    }

    .stock-notification.negative-stock {
        border-left-color: #f44336;
    }

    .stock-notification-content {
        font-size: 13px;
        color: #333;
        margin-right: 20px;
    }

    .stock-notification .close-notification {
        position: absolute;
        right: 8px;
        top: 8px;
        cursor: pointer;
        color: #666;
    }

    .stock-notification .product-name {
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .stock-notification .stock-count {
        font-size: 12px;
    }

    .stock-notification .stock-warning {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        margin-top: 5px;
    }

    .stock-notification.negative-stock .stock-warning {
        background-color: #ffebee;
        color: #f44336;
    }

    .stock-notification .stock-warning {
        background-color: #fff3e0;
        color: #ff9800;
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
</style>
</head>
<body class="hold-transition skin-purple-light sidebar-mini">
    <div class="wrapper">

        @includeIf('layouts.header')

        @includeIf('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    @yield('title')
                </h1>
                <ol class="breadcrumb">
                    @section('breadcrumb')
                        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    @show
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
                @yield('content')

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @includeIf('layouts.footer')
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="{{ asset('AdminLTE-2/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- Moment -->
    <script src="{{ asset('AdminLTE-2/bower_components/moment/min/moment.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('AdminLTE-2/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-2/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('AdminLTE-2/dist/js/adminlte.min.js') }}"></script>
    <!-- Validator -->
    <script src="{{ asset('js/validator.min.js') }}"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

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
</body>
<style>
    #temp-barcode {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
</style>
</html>
