@extends('layouts.coreui-master')

@section('title')
    Daftar Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Penjualan</li>
@endsection

@section('content')
<!-- Payment Status Notification -->
<div id="payment-notification" class="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: none; min-width: 300px;"></div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Range picker akan ditempel di samping search lewat JS -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-penjualan">
                        <thead>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Kode Member</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Diskon</th>
                            <th>Total Bayar</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Kasir</th>
                            <th width="15%"><i class="cil-settings"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan.detail')
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    let table, table1;
    // Default: tanpa filter (show all)
    let activeStart = null;
    let activeEnd = null;

    $(function () {
        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('penjualan.data') }}',
                data: function (d) {
                    // Kirim filter hanya jika user memilih range
                    if (activeStart && activeEnd) {
                        d.start_date = activeStart;
                        d.end_date = activeEnd;
                    }
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'kode_member'},
                {data: 'total_item'},
                {data: 'total_harga'},
                {data: 'diskon'},
                {data: 'bayar'},
                {data: 'metode_pembayaran'},
                {data: 'status_pembayaran'},
                {data: 'kasir'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            language: {
                search: "", // ilangin tulisan "Search:"
                searchPlaceholder: "Cari penjualan..." // placeholder di dalam box
            }
        });

        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            pageLength:-1,
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'subtotal'},
            ]
        })

        // Buat input range dan tempel di sebelah right search box DataTables
        const rangeHtml = `
            <div class="input-group input-group-sm ms-2" id="sales-range-wrapper" style="max-width: 320px;">
                <span class="input-group-text"><i class="cil-calendar"></i></span>
                <input type="text" id="sales-daterange" class="form-control form-control-sm" placeholder="Semua waktu" autocomplete="off" />
                <button class="btn btn-link btn-clear-range" type="button" id="btn-clear-range" title="Reset"><i class="mynaui-x"></i></button>
            </div>`;
        // Tempel di sebelah kiri search (prepend)
        const $filter = $('.dataTables_filter');
        if ($filter.length) {
            $filter.prepend(rangeHtml);
        }

        // Date Range Picker (samain preset & locale dengan dashboard)
        const $range = $('#sales-daterange');
        $range.daterangepicker({
            opens: 'left',
            showDropdowns: true,
            linkedCalendars: false,
            drops: 'auto',
            autoUpdateInput: false, // biar placeholder "Semua waktu" sampai user apply
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
            },
            buttonClasses: 'btn btn-sm',
            applyButtonClasses: 'btn-primary',
            cancelButtonClasses: 'btn-light'
        }, function(start, end, label) {
            // Update state dari parameter callback (pasti nilai terbaru)
            activeStart = start.format('YYYY-MM-DD');
            activeEnd = end.format('YYYY-MM-DD');
            $range.val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            table.ajax.reload();
        });

        // Jaga-jaga: saat event apply dipicu
        $range.on('apply.daterangepicker', function(ev, picker) {
            activeStart = picker.startDate.format('YYYY-MM-DD');
            activeEnd = picker.endDate.format('YYYY-MM-DD');
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            table.ajax.reload();
        });

        // Reset filter -> tampilkan semua data
        $('#btn-clear-range').on('click', function(){
            activeStart = null;
            activeEnd = null;
            $range.val('');
            table.ajax.reload();
        });
    });

    function showDetail(url) {
        $('#modal-detail').modal('show');

        table1.ajax.url(url);
        table1.ajax.reload();
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
    
    // Payment Status Checking Function
    function checkPaymentStatus(url) {
        // Show loading notification
        showNotification('Checking payment status...', 'info');
        
        $.post(url, {
            '_token': $('[name=csrf-token]').attr('content')
        })
        .done((response) => {
            if (response.success) {
                const statusColors = {
                    'LUNAS': 'success',
                    'PENDING': 'warning', 
                    'GAGAL': 'danger',
                    'DIBATALKAN': 'default'
                };
                
                const alertType = statusColors[response.status] || 'info';
                showNotification(response.message, alertType);
                
                // Reload table to show updated status
                table.ajax.reload();
            } else {
                showNotification(response.message || 'Failed to check payment status', 'danger');
            }
        })
        .fail((errors) => {
            showNotification('Error checking payment status', 'danger');
        });
    }
    
    // Notification System
    function showNotification(message, type = 'info', duration = 5000) {
        const notification = $('#payment-notification');
        const alertClass = 'alert-' + type;
        
        // Remove any existing alert classes
        notification.removeClass('alert-success alert-info alert-warning alert-danger alert-default');
        
        // Add new alert class and show notification
        notification.addClass(alertClass)
                   .html('<strong>' + message + '</strong>')
                   .fadeIn();
                   
        // Auto hide after duration
        setTimeout(() => {
            notification.fadeOut();
        }, duration);
    }
</script>
@endpush
