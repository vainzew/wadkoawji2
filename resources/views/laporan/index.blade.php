@extends('layouts.coreui-master')

@section('title', 'Laporan Pendapatan')

@push('css')
{{-- CDN for daterangepicker --}}
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Laporan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-inline">
                        <div class="form-group">
                            <label for="daterange" class="mr-2">Periode</label>
                            <input type="text" id="daterange" class="form-control" style="width: 300px;">
                        </div>
                    </div>
                    <div>
                        <button id="export-pdf-btn" class="btn-with-icon btn-main"><i class="mynaui-printer"></i> Export PDF</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="report-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Kas</th>
                                <th>Penjualan</th>
                                <th>Pembelian</th>
                                <th>Pengeluaran</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Total Pendapatan</th>
                                <th id="total-pendapatan"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- CDN for daterangepicker --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    let table;
    let startDate = '{{ $tanggalAwal }}';
    let endDate = '{{ $tanggalAkhir }}';

    $(function() {
        // 1. Initialize Date Range Picker
        $('#daterange').daterangepicker({
            startDate: moment(startDate),
            endDate: moment(endDate),
            ranges: {
               'Hari Ini': [moment(), moment()],
               'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
               '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
               'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
               'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function(start, end, label) {
            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
            table.ajax.reload(); // Reload table on date change
        });

        // 2. Initialize DataTable
        table = $('#report-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route("laporan.data") }}',
                data: function (d) {
                    d.tanggal_awal = startDate;
                    d.tanggal_akhir = endDate;
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'kas'},
                {data: 'penjualan'},
                {data: 'pembelian'},
                {data: 'pengeluaran'},
                {data: 'pendapatan'}
            ],
            bSort: false,
            bPaginate: false,
            searching: false,
            info: false,
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[\Rp. ,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };

                // Total over all pages
                total = api
                    .column(6) // Pendapatan column
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(6).footer()).html('Rp ' + total.toLocaleString('id-ID'));
            }
        });

        // 3. Export button handler
        $('#export-pdf-btn').on('click', function() {
            let url = `{{ route('laporan.export_pdf') }}?tanggal_awal=${startDate}&tanggal_akhir=${endDate}`;
            window.open(url, '_blank');
        });
    });
</script>
@endpush
