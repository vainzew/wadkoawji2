@extends('layouts.coreui-master')

@section('title')
    Edit Profil
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Edit Profil</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Pengaturan Profil</h5>
            </div>
            <form action="{{ route('user.update_profil') }}" method="post" id="form-profil" class="form-profil" data-toggle="validator" enctype="multipart/form-data" onsubmit="return false;">
                @csrf
                <div class="card-body">
                    <div class="alert alert-success d-none" role="alert">
                        <i class="fa fa-check me-1"></i> Perubahan berhasil disimpan
                    </div>

                    <div class="row mb-3">
                        <label for="name" class="col-lg-3 col-form-label">Nama</label>
                        <div class="col-lg-5">
                            <input type="text" name="name" class="form-control" id="name" required autofocus value="{{ $profil->name }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="foto" class="col-lg-3 col-form-label">Foto Profil</label>
                        <div class="col-lg-5">
                            <input type="file" name="foto" class="form-control" id="foto" accept="image/*"
                                   onchange="preview('.tampil-foto', this.files[0])">
                            <div class="form-text">Format: JPG/PNG. Disarankan gambar kotak.</div>
                            <div class="tampil-foto mt-3">
                                @if(!empty($profil->foto))
                                    <img src="{{ url($profil->foto) }}" class="img-fluid rounded" style="max-width: 180px; max-height: 180px;">
                                @else
                                    <div class="text-muted small">Belum ada foto</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="old_password" class="col-lg-3 col-form-label">Password Lama</label>
                        <div class="col-lg-5">
                            <input type="password" name="old_password" id="old_password" class="form-control" minlength="6">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password" class="col-lg-3 col-form-label">Password Baru</label>
                        <div class="col-lg-5">
                            <input type="password" name="password" id="password" class="form-control" minlength="6">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password_confirmation" class="col-lg-3 col-form-label">Konfirmasi Password</label>
                        <div class="col-lg-5">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" data-match="#password">
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
@endsection

@push('scripts')
<script>
    $(function () {
        $('#old_password').on('keyup', function () {
            if ($(this).val() != "") $('#password, #password_confirmation').attr('required', true);
            else $('#password, #password_confirmation').attr('required', false);
        });

        $('.form-profil').validator().on('submit', function (e) {
            e.preventDefault(); 
                $.ajax({
                    url: $('.form-profil').attr('action'),
                    type: $('.form-profil').attr('method'),
                    data: new FormData($('.form-profil')[0]),
                    async: false,
                    processData: false,
                    contentType: false
                })
                .done(response => {
                    const data = response.data || {};
                    if (data.name) {
                        $('[name=name]').val(data.name);
                    }
                    if (data.foto) { 
                        const base = `{{ url('/') }}`; 
                        const bust = `?v=${Date.now()}`; 
                        $('.tampil-foto').html(`<img src="${base}${data.foto}${bust}" class=\"img-fluid rounded\" style=\"max-width:180px; max-height:180px;\">`); 
                        $('.img-profil').attr('src', `${base}${data.foto}${bust}`); 
                    } 

                    $('.alert').removeClass('d-none').hide().fadeIn();
                    setTimeout(() => {
                        $('.alert').fadeOut();
                    }, 3000);
                })
                .fail(errors => {
                    if (errors.status == 422) {
                        const msg = (errors.responseJSON && (errors.responseJSON.message || errors.responseJSON)) || 'Validasi gagal';
                        alert(msg); 
                    } else {
                        alert('Tidak dapat menyimpan data');
                    }
                    return;
                }); 
            return false; 
        }); 
    });
</script>
@endpush
@push('scripts')
<script>
// Override submit to avoid raw JSON navigation and update avatar live
$(function(){
  $('#form-profil').off('submit.profilefix').on('submit.profilefix', function(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    const $form = $(this);
    $.ajax({
      url: $form.attr('action'),
      type: $form.attr('method') || 'POST',
      data: new FormData($form[0]),
      processData: false,
      contentType: false
    }).done(function(response){
      const data = response.data || {};
      if (data.foto) {
        const base = `{{ url('/') }}`; const bust = `?v=${Date.now()}`;
        $('.tampil-foto').html(`<img src="${base}${data.foto}${bust}" class="img-fluid rounded" style="max-width:180px; max-height:180px;">`);
        $('.img-profil').attr('src', `${base}${data.foto}${bust}`);
      }
      if (data.name) $('[name=name]').val(data.name);
      $('.alert').removeClass('d-none').hide().fadeIn();
      setTimeout(() => { $('.alert').fadeOut(); }, 3000);
    }).fail(function(xhr){
      if (xhr.status == 422) {
        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON)) || 'Validasi gagal';
        alert(msg);
      } else {
        alert('Tidak dapat menyimpan data');
      }
    });
    return false;
  });
});
</script>
@endpush
