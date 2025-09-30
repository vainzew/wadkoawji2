<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-title">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="post" class="form-horizontal">
                @csrf
                @method('post')

                <div class="modal-header">
                    <h5 class="modal-title" id="modal-form-title"></h5>
                    <button type="button" class="btn-close" id="closeModalBtn" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tambahkan field input untuk barcode scanner -->
                    <div class="mb-3 row">
                        <label for="barcode" class="col-sm-3 col-form-label">Barcode</label>
                        <div class="col-sm-9">
                            <input type="text" name="barcode" id="barcode" class="form-control" autofocus placeholder="Scan barcode">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Field lainnya (nama_produk, kategori, merk, harga_beli, harga_jual, diskon, stok) -->
                    <div class="mb-3 row">
                        <label for="nama_produk" class="col-sm-3 col-form-label">Nama</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="id_kategori" class="col-sm-3 col-form-label">Kategori</label>
                        <div class="col-sm-9">
                            <select name="id_kategori" id="id_kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategori as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="merk" class="col-sm-3 col-form-label">Merk</label>
                        <div class="col-sm-9">
                            <input type="text" name="merk" id="merk" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="harga_beli" class="col-sm-3 col-form-label">Harga Beli</label>
                        <div class="col-sm-9">
                            <input type="number" name="harga_beli" id="harga_beli" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="harga_jual" class="col-sm-3 col-form-label">Harga Jual</label>
                        <div class="col-sm-9">
                            <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <!-- Field diskon dihilangkan karena sekarang pake sistem promo terpisah -->
                    <!-- <div class="mb-3 row">
                        <label for="diskon" class="col-sm-3 col-form-label">Diskon</label>
                        <div class="col-sm-9">
                            <input type="number" name="diskon" id="diskon" class="form-control" value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div> -->
                    <div class="mb-3 row">
                        <label for="stok" class="col-sm-3 col-form-label">Stok</label>
                        <div class="col-sm-9">
                            <input type="number" name="stok" id="stok" class="form-control" required value="0">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <!-- FIELD BARU: Expired Date -->
                    <div class="mb-3 row">
                        <label for="expired_at" class="col-sm-3 col-form-label">Expired Date</label>
                        <div class="col-sm-9">
                            <input type="text" name="expired_at" id="expired_at" class="form-control" placeholder="MM/YY" maxlength="5" pattern="^(0[1-9]|1[0-2])\/[0-9]{2}$">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Format: MM/YY (contoh: 12/25)</small>
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

<script>
// Fungsi untuk format input expired date
document.getElementById('expired_at').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Hapus semua karakter non-digit
    
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    
    e.target.value = value;
});

// Validasi expired date
document.getElementById('expired_at').addEventListener('blur', function(e) {
    const value = e.target.value;
    const pattern = /^(0[1-9]|1[0-2])\/[0-9]{2}$/;
    
    if (value && !pattern.test(value)) {
        alert('Format expired date tidak valid. Gunakan format MM/YY');
        e.target.focus();
    }
});

const barcodeInput = document.getElementById('barcode');
barcodeInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault(); // Mencegah form submit
        const barcode = e.target.value.trim();

        if (barcode.length > 0) {
            console.log('Barcode scanned:', barcode);

            // Kosongkan input untuk scan berikutnya
            e.target.value = '';
        }
    }
});
</script>