<div class="modal fade" id="modal-member" tabindex="-1" role="dialog" aria-labelledby="modal-member-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-member-title">Pilih Member</h5>
                <button type="button" class="btn-close" id="closeModalMemberBtn" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-member">
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
                            @foreach ($member as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->telepon }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="pilihMember('{{ $item->id_member }}', '{{ $item->kode_member }}')">
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