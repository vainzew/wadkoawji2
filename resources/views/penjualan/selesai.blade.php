@extends('layouts.coreui-master')

@section('title')
    Transaksi Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaksi Selesai</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success alert-dismissible">
                    <h4><i class="fa fa-check icon"></i> Data Transaksi telah selesai.</h4>
                    <p>Terima kasih atas transaksi Anda.</p>
                </div>
            </div>
            <div class="card-footer">
                @if ($setting->tipe_nota == 1)
                <button class="btn-with-icon btn-another" onclick="printNota('{{ route('transaksi.nota_kecil') }}')">
                    <i class="mynaui-printer"></i> Cetak Ulang Nota
                </button>
                @else
                <button class="btn-with-icon btn-another" onclick="printNota('{{ route('transaksi.nota_besar') }}')">
                    <i class="mynaui-printer"></i> Cetak Ulang Nota
                </button>
                @endif
                <a href="{{ route('transaksi.baru') }}" class="btn-with-icon btn-main">
                    <i class="mynaui-plus"></i> Transaksi Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan iframe tersembunyi -->
<iframe id="printFrame" style="display: none; width: 0; height: 0;"></iframe>
@endsection

@push('scripts')
<script>
function printNota(url) {
    const frame = document.getElementById('printFrame');
    frame.src = url;
    
    frame.onload = function() {
        try {
            frame.contentWindow.print();
        } catch (e) {
            console.error('Print failed:', e);
            window.location.href = url;
        }
    };
}

// Auto print saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    const notaUrl = '{{ route('transaksi.nota_kecil') }}';
    printNota(notaUrl);
});
</script>
@endpush