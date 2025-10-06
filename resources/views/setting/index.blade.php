@extends('layouts.coreui-master')

@section('title')
    Pengaturan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Pengaturan</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pengaturan Sistem</h5>
                </div>
                <form action="{{ route('setting.update') }}" method="post" class="form-setting" data-toggle="validator" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info alert-dismissible fade show" style="display: none;">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <i class="icon fa fa-check"></i> Perubahan berhasil disimpan
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">Informasi Perusahaan</h6>
                                <hr class="mt-0">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="nama_perusahaan" class="col-lg-3 col-form-label">Nama Perusahaan</label>
                            <div class="col-lg-9">
                                <input type="text" name="nama_perusahaan" class="form-control" id="nama_perusahaan" required autofocus>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="telepon" class="col-lg-3 col-form-label">Telepon</label>
                            <div class="col-lg-9">
                                <input type="text" name="telepon" class="form-control" id="telepon" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="alamat" class="col-lg-3 col-form-label">Alamat</label>
                            <div class="col-lg-9">
                                <textarea name="alamat" class="form-control" id="alamat" rows="3" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-4 mt-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">Pengaturan Visual</h6>
                                <hr class="mt-0">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="path_logo" class="col-lg-3 col-form-label">Logo Perusahaan</label>
                            <div class="col-lg-9">
                                <input type="file" name="path_logo" class="form-control" id="path_logo"
                                    onchange="preview('.tampil-logo', this.files[0])">
                                <div class="form-text">Ukuran yang disarankan: 200x200px</div>
                                <div class="invalid-feedback"></div>
                                <div class="mt-3">
                                    <div class="tampil-logo"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="path_kartu_member" class="col-lg-3 col-form-label">Kartu Member</label>
                            <div class="col-lg-9">
                                <input type="file" name="path_kartu_member" class="form-control" id="path_kartu_member"
                                    onchange="preview('.tampil-kartu-member', this.files[0], 300)">
                                <div class="form-text">Ukuran yang disarankan: 300x200px</div>
                                <div class="invalid-feedback"></div>
                                <div class="mt-3">
                                    <div class="tampil-kartu-member"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4 mt-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">Pengaturan Transaksi</h6>
                                <hr class="mt-0">
                            </div>
                        </div>
                        
                        <!-- Pajak / PPN --> 
                        <div class="row mb-3"> 
                            <label for="diskon" class="col-lg-3 col-form-label">PPN (%)</label> 
                            <div class="col-lg-9"> 
                                <div class="d-flex align-items-center" style="gap:12px;"> 
                                    <input type="number" name="diskon" class="form-control" id="diskon" min="0" max="100" step="0.1" value="0" style="max-width:160px;"> 
                                    <div class="form-check"> 
                                        <input class="form-check-input" type="checkbox" id="tax_enabled" name="tax_enabled" value="1"> 
                                        <label class="form-check-label" for="tax_enabled">Aktif</label> 
                                    </div> 
                                </div> 
                                <div class="form-text">Isi nilai pajak (0–100). Contoh: 10 untuk 10%.</div> 
                                <div class="invalid-feedback"></div> 
                            </div> 
                        </div> 
                        
                        <div class="row mb-3">
                            <label for="tipe_nota" class="col-lg-3 col-form-label">Tipe Nota</label>
                            <div class="col-lg-4">
                                <select name="tipe_nota" class="form-select" id="tipe_nota" required>
                                    <option value="1">Nota Kecil</option>
                                    <option value="2">Nota Besar</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        showData();

        $('.form-setting').on('submit', function (e) {
            e.preventDefault(); // Always prevent default submission
            
            // Show loading state
            const submitBtn = $(this).find('button[type=submit]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...').prop('disabled', true);
            
            $.ajax({
                url: $('.form-setting').attr('action'),
                type: $('.form-setting').attr('method'),
                data: new FormData($('.form-setting')[0]),
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Success response:', response);
                    showData();
                    $('.alert').fadeIn().removeClass('d-none');

                    setTimeout(() => {
                        $('.alert').fadeOut();
                    }, 3000);
                    
                    // Reset button
                    submitBtn.html(originalText).prop('disabled', false);
                },
                error: function(xhr) {
                    console.log('Error response:', xhr);
                    alert('Tidak dapat menyimpan data: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText));
                    // Reset button
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });
    });

    function showData() {
        $.ajax({
            url: '{{ route('setting.show') }}',
            type: 'GET',
            success: function(response) {
                console.log('Settings data:', response);
                if (response && response.data) {
                    const data = response.data;
                    $('[name=nama_perusahaan]').val(data.nama_perusahaan || '');
                    $('[name=telepon]').val(data.telepon || '');
                    $('[name=alamat]').val(data.alamat || '');
                    $('[name=diskon]').val(data.diskon !== null ? data.diskon : 0);
                    $('[name=tax_enabled]').prop('checked', !!data.tax_enabled);
                    $('[name=tipe_nota]').val(data.tipe_nota || 1);
                    
                    // Update page title
                    if (data.nama_perusahaan) {
                        $('title').text(data.nama_perusahaan + ' | Pengaturan');
                    }
                    
                    // Update logo preview
                    if (data.path_logo) {
                        $('.tampil-logo').html(`<img src="{{ url('/') }}${data.path_logo}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">`);
                    }
                    
                    // Update member card preview
                    if (data.path_kartu_member) {
                        $('.tampil-kartu-member').html(`<img src="{{ url('/') }}${data.path_kartu_member}" class="img-fluid rounded" style="max-width: 300px; max-height: 200px;">`);
                    }
                    
                    // Update favicon
                    if (data.path_logo) {
                        $('[rel=icon]').attr('href', `{{ url('/') }}${data.path_logo}`);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading settings data:', xhr);
                alert('Tidak dapat menampilkan data: ' + xhr.status + ' ' + xhr.statusText);
            }
        });
    }
</script>
@endpush
