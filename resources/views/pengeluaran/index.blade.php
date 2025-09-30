@extends('layouts.coreui-master')

@section('title')
    Daftar Pengeluaran
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Pengeluaran</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <button onclick="addForm('{{ route('pengeluaran.store') }}')" class="btn-with-icon btn-main"><i class="mynaui-plus"></i> Tambah</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th width="15%"><i class="cil-settings"></i></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('pengeluaran.form')
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
                url: '{{ route('pengeluaran.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'created_at'},
                {data: 'deskripsi'},
                {data: 'nominal'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            language: {
                search: "", // ilangin tulisan "Search:"
                searchPlaceholder: "Cari pengeluaran..." // placeholder di dalam box
            }
        });

        // Handle form submission with AJAX - using event delegation
        $(document).on('submit', '#modal-form form', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation(); // Prevent other handlers
            
            // Get form data
            var form = $(this);
            var url = form.attr('action');
            var method = form.find('[name=_method]').val() || 'post';
            var formData = form.serialize();
            
            // Add CSRF token
            formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');
            
            // Handle different HTTP methods
            if (method.toLowerCase() === 'put') {
                // For PUT requests, we need to send as POST with _method parameter
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
                    } else {
                        alert('Terjadi kesalahan: ' + (response.message || 'Data gagal disimpan'));
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Tidak dapat menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
            
            return false; // Prevent default submission
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Pengeluaran');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=deskripsi]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Pengeluaran');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=deskripsi]').focus();

        $.get(url)
            .done((response) => {
                if (response.status === 'success' && response.data) {
                    $('#modal-form [name=deskripsi]').val(response.data.deskripsi);
                    $('#modal-form [name=nominal]').val(response.data.nominal);
                } else {
                    alert('Tidak dapat menampilkan data');
                }
            })
            .fail((xhr) => {
                let errorMessage = 'Tidak dapat menampilkan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
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
                    } else {
                        alert('Terjadi kesalahan: ' + (response.message || 'Data gagal dihapus'));
                    }
                })
                .fail((xhr) => {
                    let errorMessage = 'Tidak dapat menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    return;
                });
        }
    }
</script>
@endpush