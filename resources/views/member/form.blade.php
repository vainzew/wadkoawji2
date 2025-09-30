<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-title">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-form-title"></h5>
                    <button type="button" class="btn-close" id="closeModalBtn" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="nama" class="col-lg-3 col-form-label">Nama</label>
                        <div class="col-lg-9">
                            <input type="text" name="nama" id="nama" class="form-control" required autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="telepon" class="col-lg-3 col-form-label">Telepon</label>
                        <div class="col-lg-9">
                            <input type="text" name="telepon" id="telepon" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="alamat" class="col-lg-3 col-form-label">Alamat</label>
                        <div class="col-lg-9">
                            <textarea name="alamat" id="alamat" rows="3" class="form-control"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="closeModalBtnFooter" data-coreui-dismiss="modal" data-bs-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>