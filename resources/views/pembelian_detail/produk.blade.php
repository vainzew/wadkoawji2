<div class="modal fade" id="modal-produk" tabindex="-1" role="dialog" aria-labelledby="modal-produk-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-produk-title">Pilih Produk</h5>
                <button type="button" class="btn-close" id="closeModalProdukBtn" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-produk">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga Beli</th>
                                <th width="100px" class="text-center"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produk as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td><span class="badge bg-success">{{ $item->kode_produk }}</span></td>
                                    <td>{{ $item->nama_produk }}</td>
                                    <td>{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="pilihProduk('{{ $item->id_produk }}', '{{ $item->kode_produk }}')">
                                            <i class="fa fa-check-circle"></i>
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>