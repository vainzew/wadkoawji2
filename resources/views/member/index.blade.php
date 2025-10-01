@extends('layouts.coreui-master')

@section('title')
    Daftar Member
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Member</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <button onclick="addForm('{{ route('member.store') }}')" class="btn-with-icon btn-main" data-coreui-toggle="modal" data-coreui-target="#modal-form"><i class="mynaui-plus"></i> Tambah</button>
                <button onclick="cetakMember('{{ route('member.cetak_member') }}')" class="btn-with-icon btn-another"><i class="mynaui-printer"></i> Cetak Member</button>
            </div>
            <div class="card-body">
                <form action="" method="post" class="form-member">
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
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th width="15%"><i class="cil-settings"></i></th>
                            </thead>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('member.form')
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
                url: '{{ route('member.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_member'},
                {data: 'nama'},
                {data: 'telepon'},
                {data: 'alamat'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            language: {
                search: "", // ilangin tulisan "Search:"
                searchPlaceholder: "Cari member..." // placeholder di dalam box
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
        $('#modal-form .modal-title').text('Tambah Member');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Member');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama]').focus();

        $.get(url)
            .done((response) => {
                if (response.status === 'success' && response.data) {
                    $('#modal-form [name=nama]').val(response.data.nama);
                    $('#modal-form [name=telepon]').val(response.data.telepon);
                    $('#modal-form [name=alamat]').val(response.data.alamat);
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

    function cetakMember(url) {
        if ($('input:checked').length < 1) {
            alert('Pilih data yang akan dicetak');
            return;
        } else {
            $('.form-member')
                .attr('target', '_blank')
                .attr('action', url)
                .submit();
        }
    }
</script>
@endpush
