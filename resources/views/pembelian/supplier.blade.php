<div class="modal fade" id="modal-supplier" tabindex="-1" role="dialog" aria-labelledby="modal-supplier-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-supplier-title">Pilih Supplier</h5>
                <button type="button" class="btn-close" id="closeModalSupplierBtn" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-supplier">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th width="100px" class="text-center"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplier as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->telepon }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('pembelian.create', $item->id_supplier) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-check-circle"></i>
                                            Pilih
                                        </a>
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