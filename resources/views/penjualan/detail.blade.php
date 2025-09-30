<style>
    .table-primary th {
        color: white !important;
        background-color: #154795 !important;
    }
</style>

<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detail-title" data-coreui-backdrop="true" data-coreui-keyboard="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detail-title">Detail Penjualan</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-detail">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>