<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-title">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="post">
                @csrf
                @method('post')
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-form-title"></h5>
                    <button type="button" class="btn-close" id="closeModalBtn" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="nama_kategori" class="col-sm-3 col-form-label">Kategori</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="closeModalBtnFooter" data-coreui-dismiss="modal" data-bs-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>