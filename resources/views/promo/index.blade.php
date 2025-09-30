@extends('layouts.coreui-master')

@section('title')
    Daftar Promo
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Promo</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <button onclick="addForm('{{ route('promo.store') }}')" class="btn-with-icon btn-main" data-coreui-toggle="modal" data-coreui-target="#modal-form"><i class="mynaui-plus"></i> Tambah</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-promo">
                        <thead>
                            <th width="5%">No</th>
                            <th>Nama Promo</th>
                            <th>Tipe Promo</th>
                            <th>Detail Promo</th>
                            <th>Produk Terkait</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('promo.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table-promo').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('promo.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nama_promo'},
                {data: 'tipe_promo', render: function(data) {
                    switch(data) {
                        case 'percent_per_item': return 'Percent per Item';
                        case 'b1g1_same_item': return 'Buy 1 Get 1 (Same Item)';
                        case 'buy_a_get_b_free': return 'Buy A Get B Free';
                        default: return data;
                    }
                }},
                {data: 'detail_promo'},
                {data: 'produk_terkait'},
                {data: 'periode'},
                {data: 'status'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            language: {
                search: "", // ilangin tulisan "Search:"
                searchPlaceholder: "Cari promo..." // placeholder di dalam box
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
            
            // Show loading state
            let submitBtn = $('#modal-form button[type="submit"]');
            let originalText = submitBtn.html();
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
            
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
                },
                complete: function() {
                    // Reset button state
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
            
            return false;
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Promo');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_promo]').focus();
        
        // Reset form elements
        $('.promo-details').hide();
        
        // Clear hidden fields and displays
        $('#selected_produk_percent, #selected_produk_b1g1').val('');
        $('#display_produk_percent, #display_produk_b1g1').val('');
        $('#count_produk_percent, #count_produk_b1g1').text('0');
        $('#id_produk_buy, #id_produk_get').val('').trigger('change');
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Promo');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');

        $.get(url)
            .done((response) => {
                if (response.status === 'success' && response.data) {
                    $('#modal-form [name=nama_promo]').val(response.data.nama_promo);
                    $('#modal-form [name=tipe_promo]').val(response.data.tipe_promo).trigger('change');
                    $('#modal-form [name=description]').val(response.data.description);
                    $('#modal-form [name=start_date]').val(response.data.start_date);
                    $('#modal-form [name=end_date]').val(response.data.end_date);
                    $('#modal-form [name=is_active]').prop('checked', response.data.is_active);
                    
                    // Set specific fields based on promo type
                    if (response.data.tipe_promo == 'percent_per_item') {
                        $('#modal-form [name=discount_percentage]').val(response.data.discount_percentage);
                        if (response.data.produk && response.data.produk.length > 0) {
                            let produkIds = response.data.produk.map(p => p.id_produk);
                            let produkNames = response.data.produk.map(p => p.nama_produk);
                            $('#selected_produk_percent').val(produkIds.join(','));
                            $('#display_produk_percent').val(produkNames.slice(0, 3).join(', ') + (produkNames.length > 3 ? ` (+${produkNames.length - 3} lainnya)` : ''));
                            $('#count_produk_percent').text(produkIds.length);
                        }
                    } else if (response.data.tipe_promo == 'buy_a_get_b_free') {
                        $('#modal-form [name=buy_quantity]').val(response.data.buy_quantity);
                        $('#modal-form [name=get_quantity]').val(response.data.get_quantity);
                        $('#modal-form [name=id_produk_buy]').val(response.data.id_produk_buy).trigger('change');
                        $('#modal-form [name=id_produk_get]').val(response.data.id_produk_get).trigger('change');
                    } else if (response.data.tipe_promo == 'b1g1_same_item') {
                        if (response.data.produk && response.data.produk.length > 0) {
                            let produkIds = response.data.produk.map(p => p.id_produk);
                            let produkNames = response.data.produk.map(p => p.nama_produk);
                            $('#selected_produk_b1g1').val(produkIds.join(','));
                            $('#display_produk_b1g1').val(produkNames.slice(0, 3).join(', ') + (produkNames.length > 3 ? ` (+${produkNames.length - 3} lainnya)` : ''));
                            $('#count_produk_b1g1').text(produkIds.length);
                        }
                    }
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