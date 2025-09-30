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

@push('scripts')
<script>
    let table, table1;

    $(function () {
        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('penjualan.data') }}',
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