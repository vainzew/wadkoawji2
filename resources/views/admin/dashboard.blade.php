@extends('layouts.coreui-master')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
    .card-stat {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(16, 24, 40, .06);
        position: relative;
        margin-bottom: 1.5rem;
    }

    .card-kategori{
        background-color: #0284c7;
        color: white;
    }
    .card-produk{
        background-color: #dc2626;
        color: white;
    }
    .card-member{
        background-color: #059669;
        color: white;
    }
    .card-supplier{
        background-color: #F97316;
        color: white;
    }

    .card-stat .card-body {
        padding: 14px 16px;
        min-height: 75px;
    }
    .card-stat .icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-stat .title {
        font-size: .9rem;
        font-weight: 400;
        opacity: .95;
        margin-top: 2px;
    }
    .card-stat .value {
        font-size: 1.75rem;
        font-weight: 500;
        line-height: 1;
        margin-top: 4px;
    }

    .card-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    transition: all 0.2s ease-in-out;
    }

    .icon-box {
        color: #fff;
        font-size: 1.2rem !important;
        padding: 18px;   /* makin gede makin besar box */
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .icon-kategori {
        background-color: #0284c7;
    }
    .icon-produk {
        background-color: #dc2626;
    }
    .icon-member {
        background-color: #059669;
    }

    .icon-supplier {
        background-color: #F97316;
    }

    .icon-box [class^="mynaui-"],
    .icon-box [class*=" mynaui-"] {
        font-size: 1.2rem !important;
    }

    .dropdown-menu-sm {
        --bs-dropdown-min-width: 8rem;
        /* default 10rem */
        padding: .25rem .4rem;
        /* lebih rapat */
        border-radius: .5rem;
    }
    .dropdown-menu-sm .dropdown-item {
        padding: .25rem .5rem;
        font-size: .875rem;
    }
    .card-stat .dropdown-toggle {
        color: #111827 !important;
        pointer-events: auto;
        border: none;
        background: transparent;
        padding: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
    }
    /* hitam */
    .card-stat .dropdown-toggle:focus {
        outline: none;
        box-shadow: none;
    }
    .card-stat .dropdown-toggle::after {
        display: none;
    }
    .card-stat .dropdown {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        z-index: 900; /* Lower than header dropdown */
    }
    .card-stat .dropdown-menu {
        position: absolute !important;
        z-index: 950 !important; /* Lower than header dropdown */
        transform: translateZ(0);
        right: 0 !important;
        left: auto !important;
    }

    /* Tab Content Styles - Button Style like daily/monthly toggle */
    .tab-buttons {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .tab-button {
        background-color:rgb(255, 255, 255);
        border: 0px !important;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #333;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tab-button:hover {
        background-color: #7B83EB;
        color: #ffffff;
    }

    .tab-button.active {
        background-color: #6366f1;
        border-color: #0d6efd;
        color: white;
    }

    /* Animated Tab Content */
    .tab-content {
        position: relative;
    }

    .tab-pane {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Table Styles */
    .table th {
        font-weight: 600;
        color: #495057;
    }

    /* Badge Styles */
    .badge-sm {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }

    /* Chart Styles */
    .chart-container {
        height: 300px;
        position: relative;
        width: 100%;
    }

    .chart-legend {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        margin-bottom: 1rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }

    /* Date Range Picker */
    .daterangepicker-input {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        width: 250px;
    }

    :root {
        --box-bg: #EEF2FF;
        /* ganti sesukamu (#FEE2E2, #E0F2FE, dll.) */
        --box-fg: #2563EB;
        /* warna ikon di dalam box */
    }
    
    /* Responsive fixes */
    @media (max-width: 768px) {
        .chart-container {
            height: 250px;
        }
        
        .tab-buttons {
            flex-direction: column;
        }
        
        .tab-button {
            width: 100%;
        }
    }
</style>

@endpush

@section('content')
<!-- Stats Cards - Back to CoreUI Style -->
<div class="row g-2 mb-2">
    <div class="col-6 col-md-6 col-lg-3">
        <a href="{{ route('kategori.index') }}" class="text-decoration-none text-dark">
            <div class="card card-stat card-kategori">
                <div class="card-body">
                    <div class="d-flex gap-3 flex-row-reverse justify-content-between align-items-center">
                        <div class="icon-box icon-kategori">
                            <i class="mynaui-table"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="title">Total Kategori</div>
                            <div class="value">{{ $kategori }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-6 col-lg-3">
        <a href="{{ route('produk.index') }}" class="text-decoration-none text-dark">
            <div class="card card-stat card-produk">
                <div class="card-body">
                    <div class="d-flex gap-3 flex-row-reverse justify-content-between align-items-center">
                        <div class="icon-box icon-produk">
                            <i class="mynaui-layers-three"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="title">Total Produk</div>
                            <div class="value">{{ $produk }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-6 col-lg-3">
        <a href="{{ route('member.index') }}" class="text-decoration-none text-dark">
            <div class="card card-stat card-member">
                <div class="card-body">
                    <div class="d-flex gap-3 flex-row-reverse justify-content-between align-items-center">
                        <div class="icon-box icon-member">
                            <i class="mynaui-user-square"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="title">Total Member</div>
                            <div class="value">{{ $member }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-6 col-lg-3">
        <a href="{{ route('supplier.index') }}" class="text-decoration-none text-dark">
            <div class="card card-stat card-supplier">
                <div class="card-body">
                    <div class="d-flex gap-3 flex-row-reverse justify-content-between align-items-center">
                        <div class="icon-box icon-supplier">
                            <i class="mynaui-truck"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="title">Total Supplier</div>
                            <div class="value">{{ $supplier }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Sales Summary & Chart Section -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Grafik Pendapatan & Pengeluaran</h5>
            </div>
            
            <!-- Toggle Period -->
            <div class="btn-group" role="group">
                <button type="button" class="btn-with-icon btn-main btn-sm" id="btn-daily" onclick="togglePeriod('daily')">
                    <i class="mynaui-calendar mr-1"></i> Harian
                </button>
                <button type="button" class="btn-with-icon btn-outline-secondary btn-sm" id="btn-monthly" onclick="togglePeriod('monthly')">
                    <i class="mynaui-calendar-check mr-1"></i> Bulanan
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="bg-light p-3 rounded mb-3">
            <div class="d-flex gap-4 align-items-center">
                <!-- Period Info -->
                <div>
                    <span class="text-muted small" id="period-info">
                        <span id="current-period">{{ tanggal_indonesia($tanggal_awal, false) }} s/d {{ tanggal_indonesia($tanggal_akhir, false) }}</span>
                    </span>
                </div>
                
                <!-- Legend -->
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 16px; height: 16px; background: #8EC5FF; border-radius: 2px;"></div>
                        <span class="small text-muted">Income</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 16px; height: 16px; background: #2B7FFF; border-radius: 2px;"></div>
                        <span class="small text-muted">Outcome</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="chart-container">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
</div>

<!-- Sales Metrics Section -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Laporan Penjualan</h5>
            </div>
            
            <!-- Date Range Picker -->
            <div class="d-flex align-items-center gap-3">
                <label class="mb-0 text-muted small">Periode:</label>
                <div class="input-group" style="width: 250px;">
                    <input type="text" name="daterange" id="daterange" class="form-control form-control-sm" 
                           value="{{ date('d/m/Y', strtotime($date_start)) }} - {{ date('d/m/Y', strtotime($date_end)) }}">
                    <span class="input-group-text">
                        <i class="cil-calendar"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Sales Metrics - Back to old style -->
        <div class="row g-3" id="sales-metrics">
            <!-- Gross Sales -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-start border-start-info py-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-2">Gross Sales</div>
                        <div class="fs-5 fw-semibold" id="gross-sales">{{ 'Rp. ' . number_format($grossSales, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Net Sales -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-start border-start-success py-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-2">Net Sales</div>
                        <div class="fs-5 fw-semibold" id="net-sales">{{ 'Rp. ' . number_format($netSales, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Gross Profit -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-start border-start-warning py-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-2">Gross Profit</div>
                        <div class="fs-5 fw-semibold" id="gross-profit">{{ 'Rp. ' . number_format($grossProfit, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        
            <!-- Transactions -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-start border-start-primary py-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-2">Total Transaksi</div>
                        <div class="fs-5 fw-semibold" id="total-transactions">{{ $totalTransactions }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Average Sale per Transaction -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-start border-start-danger py-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-2">Average Sale</div>
                        <div class="fs-5 fw-semibold" id="average-sale">{{ 'Rp. ' . number_format($averageSale, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Gross Margin -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-start border-start-dark py-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-2">Gross Margin</div>
                        <div class="fs-5 fw-semibold" id="gross-margin">{{ number_format($grossMargin, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabbed Content Section with Button Style -->
        <div class="mt-4">
            <!-- Button Style Tabs -->
            <div class="tab-buttons">
                <button class="tab-button active" data-tab="stock">Stock Peringatan</button>
                <button class="tab-button" data-tab="summary">Ringkasan Stok</button>
                <button class="tab-button" data-tab="top">Top 10 Terlaris</button>
            </div>

            <!-- Tab panes with Animation -->
            <div class="tab-content" id="dashboardTabContent">
                <!-- Stock Alert Tab -->
                <div class="tab-pane active" id="stock" role="tabpanel">
                    <div class="card mb-0">
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Stok</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lowStockProducts as $product)
                                        <tr>
                                            <td>{{ $product->nama_produk }}</td>
                                            <td class="text-center">
                                                <span class="fw-bold text-{{ $product->stok <= 5 ? 'danger' : 'warning' }}">{{ $product->stok }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($product->stok <= 0)
                                                    <span class="badge bg-danger badge-sm">HABIS</span>
                                                @elseif($product->stok <= 5)
                                                    <span class="badge bg-danger badge-sm">KRITIS</span>
                                                @else
                                                    <span class="badge bg-warning badge-sm">RENDAH</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="py-4 text-center text-success fst-italic">Semua produk stok aman</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Summary Tab -->
                <div class="tab-pane" id="summary" role="tabpanel">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center border rounded p-3">
                                        <div class="fs-4 fw-bold text-danger">{{ $stockStats['outOfStock'] }}</div>
                                        <div class="small text-muted text-uppercase">Habis Stok</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center border rounded p-3">
                                        <div class="fs-4 fw-bold text-warning">{{ $stockStats['lowStock'] }}</div>
                                        <div class="small text-muted text-uppercase">Stok Rendah</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center border rounded p-3">
                                        <div class="fs-4 fw-bold text-success">{{ $stockStats['goodStock'] }}</div>
                                        <div class="small text-muted text-uppercase">Stok Aman</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center border rounded p-3">
                                        <div class="fs-4 fw-bold text-dark">{{ $stockStats['totalProducts'] }}</div>
                                        <div class="small text-muted text-uppercase">Total Produk</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products Tab -->
                <div class="tab-pane" id="top" role="tabpanel">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">                              
                                <!-- Toggle Top Selling Type -->
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-products" onclick="toggleTopSelling('products')">
                                        Produk
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-categories" onclick="toggleTopSelling('categories')">
                                        Kategori
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 175px; overflow-y: auto;">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-3">#</th>
                                            <th class="py-3" id="top-selling-header">Produk</th>
                                            <th class="py-3 text-end">Terjual</th>
                                            <th class="py-3 text-end">Omzet</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top-selling-table">
                                        @foreach($topProducts->take(10) as $index => $product)
                                        <tr>
                                            <td class="py-3">{{ $index + 1 }}</td>
                                            <td class="py-3">{{ $product->nama_produk }}</td>
                                            <td class="py-3 text-end">{{ number_format($product->total_terjual, 0, ',', '.') }}</td>
                                            <td class="py-3 text-end">Rp {{ number_format($product->total_omzet, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentChart;
let currentPeriod = 'daily';
let currentTopSellingType = 'products';

// Data dari controller dengan format Chart.js v3
const dailyData = {
    labels: {!! json_encode($data_tanggal) !!},
    datasets: [
        {
            label: 'Income',
            data: {!! json_encode($data_income) !!},
            backgroundColor: 'rgba(142, 197, 255, 0.2)',
            borderColor: '#8EC5FF',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#8EC5FF',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        },
        {
            label: 'Outcome',
            data: {!! json_encode($data_outcome) !!},
            backgroundColor: 'rgba(43, 127, 255, 0.2)',
            borderColor: '#2B7FFF',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#2B7FFF',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }
    ]
};

const monthlyData = {
    labels: {!! json_encode($data_bulan) !!},
    datasets: [
        {
            label: 'Income',
            data: {!! json_encode($data_income_bulanan) !!},
            backgroundColor: 'rgba(142, 197, 255, 0.2)',
            borderColor: '#8EC5FF',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#8EC5FF',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        },
        {
            label: 'Outcome',
            data: {!! json_encode($data_outcome_bulanan) !!},
            backgroundColor: 'rgba(43, 127, 255, 0.2)',
            borderColor: '#2B7FFF',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#2B7FFF',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }
    ]
};

// Initialize Date Range Picker
$(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal',
            fromLabel: 'Dari',
            toLabel: 'Sampai',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            firstDay: 1
        },
        ranges: {
           'Hari Ini': [moment(), moment()],
           'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
           '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
           'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
           'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start, end, label) {
        // When date range is applied, fetch new data
        updateSalesMetrics(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        
        // Update period info for top selling
        var periodText = '';
        if (label) {
            periodText = 'Periode: ' + label;
        } else {
            periodText = 'Periode: ' + start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
        }
        $('#top-selling-period').text(periodText);
        
        // Update main period text
        $('#current-period').text(start.format('DD MMM YYYY') + ' s/d ' + end.format('DD MMM YYYY'));
    });
});

// Function to update sales metrics via AJAX
function updateSalesMetrics(startDate, endDate) {
    // Show loading state
    $('#sales-metrics').css('opacity', '0.6');
    
    $.ajax({
        url: '{{ route("dashboard.sales-metrics") }}',
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            // Update the metrics
            $('#gross-sales').text('Rp. ' + parseInt(response.metrics.grossSales).toLocaleString('id-ID'));
            $('#net-sales').text('Rp. ' + parseInt(response.metrics.netSales).toLocaleString('id-ID'));
            $('#gross-profit').text('Rp. ' + parseInt(response.metrics.grossProfit).toLocaleString('id-ID'));
            $('#total-transactions').text(response.metrics.totalTransactions);
            $('#average-sale').text('Rp. ' + parseInt(response.metrics.averageSale).toLocaleString('id-ID'));
            $('#gross-margin').text(parseFloat(response.metrics.grossMargin).toFixed(1) + '%');
            
            // Update top selling table based on current type
            if (currentTopSellingType === 'products') {
                updateTopSellingTable(response.topProducts, 'nama_produk');
            } else {
                updateTopSellingTable(response.topCategories, 'nama_kategori');
            }
            
            // Restore opacity
            $('#sales-metrics').css('opacity', '1');
        },
        error: function(xhr, status, error) {
            console.error('Error updating sales metrics:', error);
            $('#sales-metrics').css('opacity', '1');
        }
    });
}

// Function to toggle top selling type (products/categories)
function toggleTopSelling(type) {
    currentTopSellingType = type;
    
    // Update button states
    const btnProducts = document.getElementById('btn-products');
    const btnCategories = document.getElementById('btn-categories');
    
    if (type === 'products') {
        btnProducts.className = 'btn btn-primary btn-sm';
        btnCategories.className = 'btn btn-outline-secondary btn-sm';
    } else {
        btnProducts.className = 'btn btn-outline-secondary btn-sm';
        btnCategories.className = 'btn btn-primary btn-sm';
    }
    
    // Update header text
    const header = document.getElementById('top-selling-header');
    header.textContent = type === 'products' ? 'Produk' : 'Kategori';
    
    // Get current date range from daterange picker
    const dateRange = $('input[name="daterange"]').val().split(' - ');
    const startDate = moment(dateRange[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
    const endDate = moment(dateRange[1], 'DD/MM/YYYY').format('YYYY-MM-DD');
    
    // Fetch new data
    updateTopSellingData(startDate, endDate, type);
}

// Function to update top selling data
function updateTopSellingData(startDate, endDate, type) {
    $.ajax({
        url: '{{ route("dashboard.sales-metrics") }}',
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            if (type === 'products') {
                updateTopSellingTable(response.topProducts, 'nama_produk');
            } else {
                updateTopSellingTable(response.topCategories, 'nama_kategori');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating top selling data:', error);
        }
    });
}

// Function to update top selling table (unified for both products and categories)
function updateTopSellingTable(data, nameField) {
    var tableBody = $('#top-selling-table');
    tableBody.empty();
    
    if (data.length === 0) {
        tableBody.append('<tr><td colspan="4" class="text-center py-8 text-gray-500">Tidak ada data</td></tr>');
        return;
    }
    
    data.forEach(function(item, index) {
        var row = '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + item[nameField] + '</td>' +
            '<td class="text-end">' + parseInt(item.total_terjual).toLocaleString('id-ID') + '</td>' +
            '<td class="text-end">Rp ' + parseInt(item.total_omzet).toLocaleString('id-ID') +
            '</td></tr>';
        tableBody.append(row);
    });
}

// Function to toggle chart period
function togglePeriod(period) {
    currentPeriod = period;
    
    // Update button states
    const btnDaily = document.getElementById('btn-daily');
    const btnMonthly = document.getElementById('btn-monthly');
    
    if (period === 'daily') {
        btnDaily.className = 'btn-with-icon btn-main btn-sm';
        btnMonthly.className = 'btn-with-icon btn-outline-secondary btn-sm';
    } else {
        btnDaily.className = 'btn-with-icon btn-outline-secondary btn-sm';
        btnMonthly.className = 'btn-with-icon btn-main btn-sm';
    }
    
    // Update chart data
    if (currentChart) {
        currentChart.destroy();
    }
    
    const ctx = document.getElementById('salesChart').getContext('2d');
    const chartData = period === 'daily' ? dailyData : monthlyData;
    
    currentChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(1) + 'rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 13
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                            return label;
                        }
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.3,
                    borderWidth: 3
                },
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });
}

// Tab switching functionality with animation
document.addEventListener('DOMContentLoaded', function() {
    // Tab button click handlers
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update active button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Hide all panes and show target pane with animation
            tabPanes.forEach(pane => {
                if (pane.id === targetTab) {
                    pane.classList.add('active');
                } else {
                    pane.classList.remove('active');
                }
            });
        });
    });
    
    // Initialize chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    currentChart = new Chart(ctx, {
        type: 'line',
        data: dailyData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(1) + 'rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 13
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                            return label;
                        }
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.3,
                    borderWidth: 3
                },
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });
});
</script>
@endpush