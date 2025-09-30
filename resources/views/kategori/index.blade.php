@extends('layouts.coreui-master')

@section('title')
    Daftar Kategori
@endsection

@section('breadcrumb')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- Data attribute approach sebagai backup-->
                <button onclick="addForm('{{ route('kategori.store') }}')" class="btn-with-icon btn-main" data-coreui-toggle="modal" data-coreui-target="#modal-form"><i class="mynaui-plus"></i> Tambah</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th width="5%">No</th>
                            <th>Kategori</th>
                            <th width="15%"><i class="mynaui-settings"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('kategori.form')
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
                url: '{{ route('kategori.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nama_kategori'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            language: {
                search: "", // ilangin tulisan "Search:"
                searchPlaceholder: "Cari kategori..." // placeholder di dalam box
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
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form-title').text('Tambah Kategori');
        
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_kategori]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form-title').text('Edit Kategori');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_kategori]').focus();

        $.get(url)
            .done((response) => {
                if (response.status === 'success' && response.data) {
                    $('#modal-form [name=nama_kategori]').val(response.data.nama_kategori);
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
            $.post(url, {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    if (response.status === 'success') {
                        table.ajax.reload();
                        // Tidak ada alert mengganggu
                    } else {
                        // Tetap tidak ada alert mengganggu
                    }
                })
                .fail((xhr) => {
                    // Tetap tidak ada alert mengganggu
                    return;
                });
        }
    }
</script>
@endpush