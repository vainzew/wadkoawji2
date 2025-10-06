@extends('layouts.coreui-master')

@section('title')
    Daftar Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div role="group" aria-label="Kelola Produk">
                    <button onclick="addForm('{{ route('produk.store') }}')" class="btn-with-icon btn-main" data-coreui-toggle="modal" data-coreui-target="#modal-form"><i class="mynaui-plus"></i> Tambah</button>
                    <button onclick="deleteSelected('{{ route('produk.delete_selected') }}')" class="btn-with-icon btn-white"><i class="mynaui-trash"></i> Hapus</button>
                    <button onclick="cetakBarcode('{{ route('produk.cetak_barcode') }}')" class="btn-with-icon btn-another"><i class="mynaui-printer"></i> Cetak Barcode</button>
                </div>
                <div class="d-flex gap-2">
                    <button id="btn-import" type="button" class="btn-with-icon btn-another" data-coreui-toggle="modal" data-coreui-target="#modal-import">
                        <i class="mynaui-upload"></i> Import
                    </button>
                    <button id="btn-low-stock" type="button" class="btn-with-icon btn-secondary" title="Tampilkan produk stok <= 1">
                        <i class="mynaui-warning"></i> Stock Rendah
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="" method="post" class="form-produk">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <th width="5%">
                                    <input type="checkbox" name="select_all" id="select_all">
                                </th>
                                <th width="5%">No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Expired</th>
                                <th width="15%"><i class="mynaui-cog-four"></i></th>
                            </thead>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('produk.form')
<!-- Import Modal -->
<div class="modal fade" id="modal-import" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Produk</h5>
        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form-import" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">File Excel/CSV</label>
            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            <div class="form-text">Gunakan template: <a href="{{ route('produk.import.template') }}">Download Template</a></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Mode</label>
            <select name="mode" class="form-select">
              <option value="upsert" selected>Upsert (update jika ada)</option>
              <option value="insert">Insert Only</option>
            </select>
          </div>
          <div id="import-result" class="d-none alert" role="alert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-coreui-dismiss="modal">Tutup</button>
        <button type="button" id="btn-do-import" class="btn btn-primary">Import</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
    let table;
    let lowStockMode = false;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('produk.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'kategori'}, // This maps to the nama_kategori column
                {data: 'merk'}, // Make sure this matches the column name
                {
                    data: 'harga_beli',
                    render: function (data, type, row) {
                        return 'Rp. ' + data;
                    }
                },
                {
                    data: 'harga_jual',
                    render: function (data, type, row) {
                        return 'Rp. ' + data;
                    }
                },
                {data: 'stok'},
                {data: 'expired_at'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            language: {
                search: "", // ilangin tulisan "Search:"
                searchPlaceholder: "Cari produk..." // placeholder di dalam box
            }
        });

        // Toggle Low Stock filter
        $('#btn-low-stock').on('click', function() {
            lowStockMode = !lowStockMode;
            $(this).toggleClass('btn-secondary btn-main');

            if (lowStockMode) {
                // Apply server-side low_stock filter and order stok ascending (column index 8)
                const url = "{{ route('produk.data') }}" + "?low_stock=1";
                table.ajax.url(url).load();
                table.order([[8, 'asc']]).draw();
            } else {
                // Reset to default data
                table.ajax.url("{{ route('produk.data') }}").load();
            }
        });

        // Handle form submission with AJAX - mirip dengan pengeluaran (tanpa alert mengganggu)
        $(document).on('submit', '#modal-form form', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            // Get form data
            var form = $(this);
            var url = form.attr('action');
            var method = form.find('[name=_method]').val() || 'post';
            var formData = form.serialize();
            
            // Add CSRF token
            formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
            
            // Handle different HTTP methods
            if (method.toLowerCase() === 'put') {
                formData += '&_method=PUT';
            }
            
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                        // Tidak ada alert mengganggu, hanya update UI
                    } else {
                        // Tetap tidak ada alert mengganggu
                        $('#modal-form').modal('hide');
                    }
                },
                error: function(xhr) {
                    // Tetap tidak ada alert mengganggu
                    $('#modal-form').modal('hide');
                }
            });
            
            return false;
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form-title').text('Tambah Produk');
        
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=barcode]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form-title').text('Edit Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=barcode]').focus();

        $.get(url)
            .done((response) => {
                if (response.status === 'success' && response.data) {
                    $('#modal-form [name=barcode]').val(response.data.barcode);
                    $('#modal-form [name=nama_produk]').val(response.data.nama_produk);
                    $('#modal-form [name=id_kategori]').val(response.data.id_kategori);
                    $('#modal-form [name=merk]').val(response.data.merk);
                    $('#modal-form [name=harga_beli]').val(response.data.harga_beli);
                    $('#modal-form [name=harga_jual]').val(response.data.harga_jual);
                    $('#modal-form [name=diskon]').val(response.data.diskon ?? 0);
                    $('#modal-form [name=stok]').val(response.data.stok);
                    $('#modal-form [name=expired_at]').val(response.data.expired_at);
                } else {
                    $('#modal-form').modal('hide');
                }
            })
            .fail((xhr) => {
                $('#modal-form').modal('hide');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        table.ajax.reload();
                        // Tidak ada alert mengganggu
                    } else {
                        // Tetap tidak ada alert mengganggu
                    }
                },
                error: function(xhr) {
                    // Tetap tidak ada alert mengganggu
                }
            });
        }
    }

    function deleteSelected(url) {
        let checkedInputs = $('input[name="id_produk[]"]:checked');
        
        if (checkedInputs.length < 1) {
            alert('Pilih data yang akan dihapus');
            return;
        }
        
        if (confirm('Yakin ingin menghapus ' + checkedInputs.length + ' data terpilih?')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: $('.form-produk').serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        table.ajax.reload();
                        // Tidak ada alert mengganggu
                    } else {
                        // Tetap tidak ada alert mengganggu
                    }
                },
                error: function(xhr) {
                    // Tetap tidak ada alert mengganggu
                }
            });
        }
    }

    function cetakBarcode(url) {
        let checkedInputs = $('input[name="id_produk[]"]:checked');
        
        if (checkedInputs.length < 1) {
            alert('Pilih data yang akan dicetak');
            return;
        } else {
            $('.form-produk')
                .attr('target', '_blank')
                .attr('action', url)
                .submit();
        }
    }
</script>
<script>
$(function(){
  $('#btn-do-import').on('click', function(){
    const form = document.getElementById('form-import');
    const fd = new FormData(form);
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Import...');
    $('#import-result').removeClass('d-none alert-success alert-danger').addClass('alert-info').text('Mengimpor...');

    $.ajax({
      url: '{{ route('produk.import') }}',
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      success: function(res){
        const sum = res.summary || {};
        let msg = 'Selesai. Inserted: ' + (sum.inserted||0) + ', Updated: ' + (sum.updated||0) + ', Failed: ' + (sum.failed||0);
        // tampilkan detail kegagalan jika ada
        if (res.failures && res.failures.length) {
          msg += '\nDetail: ';
          res.failures.slice(0,5).forEach(function(f){
            if (f && f.row && f.errors) {
              msg += `\nBaris ${f.row}: ${f.errors.join(', ')}`;
            }
          });
          if (res.failures.length > 5) msg += `\n(+${res.failures.length-5} baris lainnya)`;
        }
        $('#import-result')
          .removeClass('alert-info alert-danger')
          .addClass('alert-success')
          .text(msg);
        $('.table').DataTable().ajax.reload();
      },
      error: function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Import gagal';
        $('#import-result')
          .removeClass('alert-info alert-success')
          .addClass('alert-danger')
          .text(msg);
      },
      complete: function(){
        $btn.prop('disabled', false).html('Import');
      }
    });
  });
});
</script>
@endpush
