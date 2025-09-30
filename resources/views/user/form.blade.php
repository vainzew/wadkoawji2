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
                        <label for="name" class="col-lg-3 col-form-label">Nama</label>
                        <div class="col-lg-9">
                            <input type="text" name="name" id="name" class="form-control" required autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="username" class="col-lg-3 col-form-label">Username</label>
                        <div class="col-lg-9">
                            <input type="text" name="username" id="username" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-lg-3 col-form-label">Email</label>
                        <div class="col-lg-9">
                            <input type="email" name="email" id="email" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="level" class="col-lg-3 col-form-label">Level</label>
                        <div class="col-lg-9">
                            <select name="level" id="level" class="form-control" required>
                                <option value="">Pilih Level</option>
                                <option value="1">Admin</option>
                                <option value="2">Kasir</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password" class="col-lg-3 col-form-label">Password</label>
                        <div class="col-lg-9">
                            <input type="password" name="password" id="password" class="form-control" 
                            required
                            minlength="6">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password_confirmation" class="col-lg-3 col-form-label">Konfirmasi Password</label>
                        <div class="col-lg-9">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                required
                                data-match="#password">
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