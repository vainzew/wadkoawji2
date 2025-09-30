<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{{ $setting->nama_perusahaan }} | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="icon" href="{{ url($setting->path_logo) }}" type="image/png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Core UI Icons -->
    <link href="https://unpkg.com/@coreui/icons@3.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@coreui/icons@3.0.0/css/free.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@coreui/icons@3.0.0/css/brand.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@coreui/icons@3.0.0/css/flag.min.css" rel="stylesheet">
    <!-- MynaUI Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mynaui/icons@0.3.9/mynaui.css" rel="stylesheet">
    <!-- DataTables with Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- DatePicker -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <style>
        /* Poppins Font Family */
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif !important;
        }
        
        /* MynaUI Icons Scaling - 1.5x larger except for dashboard and sidebar */
        .mynaui:not(.sidebar .mynaui):not(.card-stats .mynaui) {
            font-size: 1.5em !important;
        }
        
        /* Core UI Demo Style - Clean & Modern */
        :root {
            --cui-sidebar-width: 256px;
            --cui-header-height: 56px;
            --cui-bg: #f8f9fa;
            --cui-sidebar-bg: #ffffff;
            --cui-text-color: #6c757d;
            --cui-text-dark: #495057;
            --cui-border-color: #dee2e6;
            --cui-primary: #321fdb;
            --cui-sidebar-nav-link-hover-bg: rgba(0, 0, 0, 0.075);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            color: var(--cui-text-color);
            background-color: var(--cui-bg);
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Layout Structure */
        .app {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
        }
        
        /* Sidebar structure */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1020;
            width: var(--cui-sidebar-width);
            background-color: var(--cui-sidebar-bg);
            border-right: 1px solid var(--cui-border-color);
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
        }
        
        /* Sidebar states */
        .sidebar.sidebar-hidden {
            transform: translateX(-100%);
        }
        
        .sidebar.sidebar-folded {
            width: 56px;
        }
        
        /* Mobile responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.sidebar-show {
                transform: translateX(0);
            }
            
            .sidebar.sidebar-folded {
                width: var(--cui-sidebar-width);
                transform: translateX(-100%);
            }
            
            .sidebar.sidebar-folded.sidebar-show {
                transform: translateX(0);
            }
        }
        
        /* Sidebar Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 0 1rem;
            height: var(--cui-header-height);
            border-bottom: 1px solid var(--cui-border-color);
            background-color: var(--cui-sidebar-bg);
        }
        
        .sidebar-brand-full {
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand-full img {
            height: 32px;
            margin-right: 8px;
        }
        
        .sidebar-brand-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--cui-text-dark);
            margin: 0;
        }
        
        .sidebar.sidebar-folded .sidebar-brand-full {
            display: none;
        }
        
        .sidebar-brand-minimized {
            display: none;
        }
        
        .sidebar.sidebar-folded .sidebar-brand-minimized {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Sidebar Navigation - Clean scrollbar */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
            list-style: none;
            overflow-x: hidden;
            overflow-y: auto;
            flex: 1;
            position: relative;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 8px;
            background: transparent;
            opacity: 0;
            transition: opacity 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .sidebar-nav:hover::-webkit-scrollbar {
            opacity: 1;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            margin: 4px 0;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.3));
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.25s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .sidebar-nav:hover::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.45));
            border-color: rgba(255, 255, 255, 0.3);
            transform: scaleX(1.1);
        }
        
        .sidebar-nav::-webkit-scrollbar-button {
            display: none !important;
            height: 0 !important;
            width: 0 !important;
        }
        
        .sidebar-nav {
            scrollbar-width: none;
        }
        
        .sidebar-nav:hover {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
        }
        
        .sidebar-nav .nav-item:first-child {
            margin-top: 0.8rem;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem 0.75rem 1.25rem;
            color: #1e1e1e;
            text-decoration: none;
            background-color: transparent;
            border: 0;
            border-left: 3px solid transparent;
            transition: all 0.15s ease-in-out;
            font-size: 1rem;
        }
        
        .nav-link:hover {
            color: var(--cui-text-dark);
            background-color: var(--cui-sidebar-nav-link-hover-bg);
        }
        
        .nav-link.active {
            color: var(--cui-primary);
            background-color: rgba(50, 31, 219, 0.1);
            border-left-color: var(--cui-primary);
        }
        
        .nav-link.active .nav-icon {
            color: var(--cui-text-dark);
        }
        
        .nav-link:hover .nav-icon {
            color: var(--cui-text-dark);
        }
        
        .nav-icon {
            flex-shrink: 0;
            width: 1.2rem;
            margin-right: 0.95rem;
            font-size: 1.2rem;
            text-align: center;
            color: #9ca3af;
            transition: color 0.15s ease-in-out;
        }
        
        .sidebar.sidebar-folded .nav-link {
            padding-left: 1rem;
            padding-right: 1rem;
            justify-content: center;
        }
        
        .sidebar.sidebar-folded .nav-text {
            display: none;
        }
        
        .sidebar.sidebar-folded .nav-icon {
            margin-right: 0;
        }
        
        /* Sidebar Footer */
        .sidebar-footer {
            flex-shrink: 0;
            background-color: var(--cui-sidebar-bg);
            border-top: 1px solid var(--cui-border-color);
            padding: 0.75rem;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-toggler-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
        }
        
        .sidebar-toggler-btn:hover {
            background-color: var(--cui-sidebar-nav-link-hover-bg);
        }
        
        .sidebar-toggler-btn .nav-icon {
            margin: 0 !important;
            font-size: 1.1rem;
            color: var(--cui-text-color);
            transition: color 0.15s ease-in-out;
        }
        
        .sidebar-toggler-btn:hover .nav-icon {
            color: var(--cui-text-dark);
        }
        
        /* Nav Titles */
        .nav-title {
            padding: 0.9rem 1rem 0.35rem 1.25rem;
            margin-top: 0.7rem;
            font-size: 0.76rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        
        .nav-title:first-of-type {
            margin-top: 0;
        }
        
        .sidebar.sidebar-folded .nav-title {
            display: none;
        }
        
        /* === TOOLTIP FIX - NO MORE JUMPING === */
        
        /* Only show tooltips when sidebar is folded AND desktop */
        .sidebar:not(.sidebar-folded) .tooltip {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
        
        @media (max-width: 992px) {
            .tooltip {
                display: none !important;
            }
        }
        
        /* Fixed tooltip positioning - no animation */
        .sidebar.sidebar-folded .tooltip {
            z-index: 1080 !important;
            /* Disable ALL animations and transforms */
            transform: none !important;
            animation: none !important;
            transition: none !important;
        }
        
        .sidebar.sidebar-folded .tooltip.fade {
            transition: none !important;
        }
        
        .sidebar.sidebar-folded .tooltip-inner {
            background-color: rgba(0, 0, 0, 0.95) !important;
            color: #ffffff !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transform: none !important;
            transition: none !important;
        }
        
        /* Arrow positioning */
        .sidebar.sidebar-folded .tooltip.bs-tooltip-end .tooltip-arrow,
        .sidebar.sidebar-folded .tooltip.bs-tooltip-right .tooltip-arrow {
            left: 0;
            transform: none !important;
        }
        
        .sidebar.sidebar-folded .tooltip.bs-tooltip-end .tooltip-arrow::before,
        .sidebar.sidebar-folded .tooltip.bs-tooltip-right .tooltip-arrow::before {
            border-right-color: rgba(0, 0, 0, 0.95) !important;
            transform: none !important;
        }
        
        /* Force immediate positioning */
        .sidebar.sidebar-folded .tooltip[data-popper-placement*="right"] {
            left: 64px !important; /* 56px + 8px margin */
            transform: none !important;
        }
        
        .sidebar.sidebar-folded .tooltip.show {
            opacity: 1 !important;
            transform: none !important;
        }
        
        /* Main Content Wrapper */
        .wrapper {
            display: flex;
            flex-direction: column;
            width: calc(100vw - var(--cui-sidebar-width));
            margin-left: var(--cui-sidebar-width);
            transition: all 0.25s ease;
            min-width: 0;
            overflow-x: hidden;
        }
        
        .sidebar.sidebar-hidden ~ .wrapper {
            margin-left: 0 !important;
            width: 100vw !important;
        }
        
        .sidebar.sidebar-folded ~ .wrapper {
            margin-left: 56px;
            width: calc(100vw - 56px);
        }
        
        @media (max-width: 992px) {
            .wrapper {
                margin-left: 0;
                width: 100vw !important;
            }
        }
        
        /* Header */
        .header {
            position: relative;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            height: var(--cui-header-height);
            background-color: #ffffff;
            border-bottom: 1px solid var(--cui-border-color);
        }
        
        .header-toggler {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 40px;
            margin-right: 1rem;
            background-color: transparent;
            border: 0;
            color: var(--cui-text-color);
            font-size: 1.2rem;
            transition: all 0.15s ease-in-out;
        }
        
        .header-toggler:hover {
            color: var(--cui-text-dark);
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 6px;
        }
        
        .header-toggler i {
            font-size: 1.2rem;
        }
        
        /* Header Navigation */
        .header-nav {
            display: flex !important;
            align-items: center !important;
            margin-left: auto;
            padding: 0;
            list-style: none;
            height: var(--cui-header-height) !important;
        }
        
        .header-nav-item {
            position: relative;
            display: flex !important;
            align-items: center !important;
            height: var(--cui-header-height) !important;
        }
        
        .header-nav-link {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 0.75rem !important;
            color: var(--cui-text-color);
            text-decoration: none;
            height: var(--cui-header-height) !important;
            min-height: 56px !important;
        }
        
        .header-nav-item .dropdown-menu {
            z-index: 1060 !important;
        }
        
        /* Main Content */
        .body {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow-x: hidden;
        }
        
        .main {
            flex: 1;
            padding: 1rem;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        .container-fluid {
            padding: 0;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Responsive Grid */
        .row {
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }
        
        .row > * {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            min-width: 0;
        }
        
        /* Cards */
        .card {
            border: 1px solid var(--cui-border-color);
            border-radius: 0.5rem;
            box-shadow: none;
            max-width: 100%;
            overflow-x: auto;
        }
        
        .card-header {
            padding: 1rem 1.25rem;
            background-color: #ffffff;
            border-bottom: 1px solid var(--cui-border-color);
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Content Header */
        .content-header {
            margin-bottom: 1rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--cui-text-dark);
            margin: 0 0 0.5rem 0;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
            background-color: transparent;
        }
        
        .breadcrumb-item {
            font-size: 0.875rem;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: "/";
            padding: 0 0.5rem;
            color: var(--cui-text-color);
        }
        
        .breadcrumb-item a {
            color: var(--cui-text-color);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--cui-text-color);
        }
        
        /* Modal Enhancements */
        .modal-content {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-top-left-radius: calc(0.5rem - 1px);
            border-top-right-radius: calc(0.5rem - 1px);
        }
        
        .modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #495057;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-bottom-left-radius: calc(0.5rem - 1px);
            border-bottom-right-radius: calc(0.5rem - 1px);
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5) !important;
            opacity: 1 !important;
        }
        
        .modal-backdrop.show {
            opacity: 1 !important;
        }
        
        .modal {
            z-index: 1055;
        }
        
        .modal-backdrop {
            z-index: 1050;
        }
        
        /* Button & Form Styling */
        .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }
        
        .btn-sm {
            font-size: 0.8125rem;
            padding: 0.25rem 0.5rem;
        }
        
        .btn-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        
        /* Table Styling */
        .table {
            font-size: 0.875rem;
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: #495057;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
        }
        
        /* Badge & Alert */
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35em 0.65em;
        }
        
        .alert {
            font-size: 0.875rem;
            border: none;
            border-radius: 0.375rem;
        }
        
        /* Stock Notifications */
        .notifications-container {
            position: fixed;
            top: 80px;
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
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .modal-header,
            .modal-footer {
                padding: 0.75rem 1rem;
            }
            
            .main {
                padding: 0.75rem;
            }
            
            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .row > * {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .main {
                padding: 0.5rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .row {
                margin-left: -0.25rem;
                margin-right: -0.25rem;
            }
            
            .row > * {
                padding-left: 0.25rem;
                padding-right: 0.25rem;
            }
        }
    </style>

    @stack('css')
</head>

<body>
    <div class="app">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            @includeIf('layouts.coreui.sidebar')
        </div>

        <!-- Main Content Wrapper -->
        <div class="wrapper">
            <!-- Header -->
            <header class="header">
                @includeIf('layouts.coreui.header')
            </header>
            
            <!-- Body -->
            <div class="body">
                <main class="main">
                    <div class="container-fluid">
                        <!-- Content Header -->
                        <div class="content-header">
                            <h1 class="page-title">@yield('title')</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @section('')
                                    @show
                                </ol>
                            </nav>
                        </div>

                        <!-- Page Content -->
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- CoreUI JavaScript - LOCAL VERSION! -->
    <script src="{{ asset('coreui/dist/js/coreui.bundle.min.js') }}"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- DatePicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    
    <!-- DON'T LOAD Bootstrap 3 Validator - ga compatible sama Bootstrap 5! -->
    <!-- <script src="{{ asset('js/validator.min.js') }}"></script> -->
    <!-- Modal Backdrop Fix -->
    <script>
        // Fix modal backdrop issues for consistency across all pages
        $(document).ready(function(){

    function hydrateSidebarLinksForTooltips(scope){
      var root = scope || document;
      var links = root.querySelectorAll('.sidebar-nav .nav-link[data-nav-text]:not([data-bs-toggle]), .sidebar-nav .nav-link[data-nav-text]:not([data-coreui-toggle])');
      links.forEach(function(a){
        var txt = a.getAttribute('data-nav-text') || a.textContent.trim();
        if (!a.getAttribute('title') && txt) a.setAttribute('title', txt);
        // set both so selector catches either env
        a.setAttribute('data-bs-toggle','tooltip');
        a.setAttribute('data-coreui-toggle','tooltip');
      });
    }

            // Handle modal backdrop manually
            $(document).on('click', '.modal-backdrop', function() {
                $('.modal').modal('hide');
                removeBackdrop();
            });
            
            // Close button handler with explicit backdrop removal
            $(document).on('click', '#closeModalBtn, #closeModalBtnFooter', function() {
                $('.modal').modal('hide');
                removeBackdrop();
            });
            
            // Enhanced modal show event
            $(document).on('show.coreui.modal', '.modal', function() {
                $('body').addClass('modal-open');
            });
            
            // Enhanced modal hidden event - clean up all effects
            $(document).on('hidden.coreui.modal', '.modal', function() {
                removeBackdrop();
            });
            
            // Manual backdrop removal
            function removeBackdrop() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('overflow', '');
                $('body').css('padding-right', '');
            }
        });
    </script>

    <script>
        // Core UI Sidebar functionality dengan WORKING TOOLTIPS
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            let tooltipInstances = [];
            
            // Function untuk manage tooltips
            function manageSidebarTooltips() {
                const isFolded = sidebar.hasClass('sidebar-folded');
                const isDesktop = window.innerWidth > 992;
                
                // Destroy existing tooltips first
                destroyAllTooltips();
                
                if (isFolded && isDesktop) {
                    // Wait for DOM to settle then init tooltips
                    setTimeout(() => {
                        $('.sidebar-nav .nav-link').each(function() {
                            const $link = $(this);
                            const navText = $link.attr('data-nav-text') || $link.find('.nav-text').text().trim();
                            
                            if (navText && navText !== '') {
                                // Create new tooltip instance using Bootstrap 5 native
                                const tooltipInstance = new bootstrap.Tooltip($link[0], {
                                    title: navText,
                                    placement: 'right',
                                    container: 'body',
                                    trigger: 'hover',
                                    delay: { show: 300, hide: 100 },
                                    boundary: 'viewport',
                                    customClass: 'sidebar-tooltip',
                                    offset: [0, 8]
                                });
                                
                                tooltipInstances.push({
                                    element: $link[0],
                                    instance: tooltipInstance
                                });
                            }
                        });
                        console.log('Tooltips initialized:', tooltipInstances.length);
                    }, 100);
                }
            }
            
            // Function to destroy all tooltips
            function destroyAllTooltips() {
                tooltipInstances.forEach(item => {
                    if (item.instance) {
                        item.instance.dispose();
                    }
                });
                tooltipInstances = [];
                // Clean up any orphaned tooltips
                $('.tooltip').remove();
                console.log('All tooltips destroyed');
            }
            
            // Load saved sidebar state on page load
            const savedSidebarState = localStorage.getItem('sidebar-state');
            if (savedSidebarState === 'folded' && window.innerWidth > 992) {
                sidebar.addClass('sidebar-folded');
                $('#toggleIcon').removeClass('cil-arrow-circle-left').addClass('cil-arrow-circle-right');
                // Init tooltips after state is loaded
                setTimeout(manageSidebarTooltips, 200);
            } else if (savedSidebarState === 'hidden' && window.innerWidth > 992) {
                sidebar.addClass('sidebar-hidden');
            }
            
            // Hamburger menu toggle (HIDE/SHOW sidebar completely)
            $(document).on('click', '.header-toggler', function() {
                if (window.innerWidth <= 992) {
                    // Mobile: Show/Hide sidebar
                    sidebar.toggleClass('sidebar-show');
                } else {
                    // Desktop: Hide/Show sidebar completely
                    sidebar.toggleClass('sidebar-hidden');
                    
                    // Save state
                    if (sidebar.hasClass('sidebar-hidden')) {
                        localStorage.setItem('sidebar-state', 'hidden');
                        destroyAllTooltips(); // Destroy tooltips when hidden
                    } else {
                        localStorage.removeItem('sidebar-state');
                        // Re-check tooltips if sidebar was folded
                        if (sidebar.hasClass('sidebar-folded')) {
                            setTimeout(manageSidebarTooltips, 200);
                        }
                    }
                }
            });
            
            // Sidebar Toggler (FOLD/UNFOLD)
            $(document).on('click', '.sidebar-toggler-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Only work on desktop
                if (window.innerWidth > 992) {
                    sidebar.toggleClass('sidebar-folded');
                    
                    // Update toggler icon
                    const icon = $('#toggleIcon');
                    if (sidebar.hasClass('sidebar-folded')) {
                        icon.removeClass('cil-arrow-circle-left').addClass('cil-arrow-circle-right');
                        localStorage.setItem('sidebar-state', 'folded');
                        // Init tooltips with delay
                        setTimeout(manageSidebarTooltips, 200);
                    } else {
                        icon.removeClass('cil-arrow-circle-right').addClass('cil-arrow-circle-left');
                        localStorage.removeItem('sidebar-state');
                        // Destroy tooltips immediately
                        destroyAllTooltips();
                    }
                }
            });
            
            // Handle window resize
            let resizeTimer;
            $(window).resize(function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    if (window.innerWidth > 992) {
                        sidebar.removeClass('sidebar-show');
                        // Re-init tooltips if folded
                        if (sidebar.hasClass('sidebar-folded')) {
                            manageSidebarTooltips();
                        }
                    } else {
                        sidebar.removeClass('sidebar-hidden sidebar-folded');
                        $('#toggleIcon').removeClass('cil-arrow-circle-right').addClass('cil-arrow-circle-left');
                        destroyAllTooltips();
                    }
                }, 250);
            });
            
            // Close mobile sidebar when clicking outside
            $(document).on('click', function(e) {
                if (window.innerWidth <= 992) {
                    if (!sidebar.is(e.target) && 
                        sidebar.has(e.target).length === 0 && 
                        !$(e.target).hasClass('header-toggler') && 
                        !$(e.target).closest('.header-toggler').length) {
                        sidebar.removeClass('sidebar-show');
                    }
                }
            });
            
            // Clean up tooltips on page unload
            $(window).on('beforeunload', function() {
                destroyAllTooltips();
            });
        });
        
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

  
  <!-- Tooltip init for folded sidebar (injected by ChatGPT) -->
  <script>
  (function() {
    if (window.__TOOLTIP_SLIDE_INITED_V2__) return;
    window.__TOOLTIP_SLIDE_INITED_V2__ = true;

    function onInsertedFactory(TooltipCtor, el){
      return function _onInserted(){
        try {
          var inst = (TooltipCtor.getInstance && TooltipCtor.getInstance(el)) ||
                     (TooltipCtor.getOrCreateInstance && TooltipCtor.getOrCreateInstance(el));
          var tip = inst && inst.getTipElement ? inst.getTipElement() : null;
          if (!tip) return;
          // Hide instantly for first two frames to avoid "top-left" flash
          tip.classList.add('tooltip-stealth');
          requestAnimationFrame(function(){
            requestAnimationFrame(function(){
              tip.classList.remove('tooltip-stealth');
            });
          });
        } catch(e){}
      };
    }

    function initTooltips(scope) {
      var root = scope || document;
      var sel = '[data-coreui-toggle="tooltip"], [data-bs-toggle="tooltip"]';
      var TooltipCtor = (window.coreui && window.coreui.Tooltip) || (window.bootstrap && window.bootstrap.Tooltip);
      if (!TooltipCtor) return;

      root.querySelectorAll(sel).forEach(function(el) {
        if (TooltipCtor.getInstance && TooltipCtor.getInstance(el)) return;
        try {
          var instance = new TooltipCtor(el, {
            placement: 'right',
            container: 'body',
            trigger: 'hover',
            boundary: document.body,
            animation: false,
            customClass: 'tooltip-slide',
            delay: { show: 0, hide: 0 },
            fallbackPlacements: [], // don't flip while folded to avoid sideways jumps
            popperConfig: {
              strategy: 'fixed',
              modifiers: [
                // Reduce layout thrash/jump on transformed ancestors
                { name: 'computeStyles', options: { adaptive: false } },
                // Keep tooltip fully within viewport if possible
                { name: 'preventOverflow', options: { boundary: document.body, padding: 8 } },
                // Tether to reference strongly so it doesn't wander
                { name: 'offset', options: { offset: [0, 8] } }
              ]
            }
          });

          // Hide until Popper finishes first layout
          var onInserted = onInsertedFactory(TooltipCtor, el);
          el.addEventListener('inserted.bs.tooltip', onInserted);
          el.addEventListener('inserted.coreui.tooltip', onInserted);

        } catch (e) {
          // no-op
        }
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function(){ hydrateSidebarLinksForTooltips();
      hydrateSidebarLinksForTooltips();
            initTooltips(); });
    } else {
      hydrateSidebarLinksForTooltips();
            initTooltips();
    }

    try {
      var mo = new MutationObserver(function(mutations){
        for (var i=0; i<mutations.length; i++) {
          var m = mutations[i];
          if (m.type === 'attributes' && (m.attributeName === 'class' || m.attributeName === 'style')) {
            hydrateSidebarLinksForTooltips();
            initTooltips();
            break;
          }
          if (m.addedNodes && m.addedNodes.length) {
            hydrateSidebarLinksForTooltips(m.target);
            initTooltips(m.target);
            break;
          }
        }
      });
      mo.observe(document.body, { attributes: true, childList: true, subtree: true });
    } catch (e) {}
  })();
  </script>

</body>
</html>