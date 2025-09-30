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
            <div class="card-header">
                <div role="group" aria-label="Kelola Produk">
                    <button onclick="addForm('{{ route('produk.store') }}')" class="btn-with-icon btn-main" data-coreui-toggle="modal" data-coreui-target="#modal-form"><i class="mynaui-plus"></i> Tambah</button>
                    <button onclick="deleteSelected('{{ route('produk.delete_selected') }}')" class="btn-with-icon btn-white"><i class="mynaui-trash"></i> Hapus</button>
                    <button onclick="cetakBarcode('{{ route('produk.cetak_barcode') }}')" class="btn-with-icon btn-another"><i class="mynaui-printer"></i> Cetak Barcode</button>
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
@endsection

@push('scripts')
<script>
    let table;

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
@endpush