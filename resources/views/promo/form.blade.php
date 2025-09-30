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
                        <label for="nama_promo" class="col-lg-3 col-form-label">Nama Promo</label>
                        <div class="col-lg-9">
                            <input type="text" name="nama_promo" id="nama_promo" class="form-control" required autofocus>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="tipe_promo" class="col-lg-3 col-form-label">Tipe Promo</label>
                        <div class="col-lg-9">
                            <select name="tipe_promo" id="tipe_promo" class="form-control" required>
                                <option value="">Pilih Tipe Promo</option>
                                <option value="percent_per_item">Percent per Item</option>
                                <option value="b1g1_same_item">Buy 1 Get 1 (Same Item)</option>
                                <option value="buy_a_get_b_free">Buy A Get B Free</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="description" class="col-lg-3 col-form-label">Deskripsi</label>
                        <div class="col-lg-9">
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Accordion untuk detail promo -->
                    <div class="panel-group" id="promo-accordion">
                        <!-- Percent per Item -->
                        <div class="panel panel-default promo-details" id="percent-details" style="display: none;">
                            <div class="panel-heading">
                                <h5 class="panel-title">Detail Diskon Persen</h5>
                            </div>
                            <div class="panel-body">
                                <div class="mb-3 row">
                                    <label for="discount_percentage" class="col-lg-3 col-form-label">Diskon (%)</label>
                                    <div class="col-lg-9">
                                        <input type="number" name="discount_percentage" id="discount_percentage" class="form-control" min="0" max="100" step="0.01">
                                        <div class="invalid-feedback">Masukkan persentase diskon (0-100)</div>
                                    </div>
                                </div>
                                
                                <!-- Produk Selection untuk Percent per Item -->
                                <div class="mb-3 row">
                                    <label for="produk_ids_percent" class="col-lg-3 col-form-label">Pilih Produk</label>
                                    <div class="col-lg-9">
                                        <input type="hidden" name="produk_ids[]" id="selected_produk_percent">
                                        <div class="input-group">
                                            <input type="text" id="display_produk_percent" class="form-control" placeholder="Klik untuk memilih produk..." readonly>
                                            <button type="button" class="btn btn-info" onclick="openProdukModal('percent')">
                                                <i class="fa fa-search"></i> Pilih Produk
                                            </button>
                                        </div>
                                        <small class="text-muted">Produk terpilih: <span id="count_produk_percent">0</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buy 1 Get 1 Same Item -->
                        <div class="panel panel-default promo-details" id="b1g1-details" style="display: none;">
                            <div class="panel-heading">
                                <h5 class="panel-title">Detail Buy 1 Get 1 (Same Item)</h5>
                            </div>
                            <div class="panel-body">
                                <!-- Produk Selection untuk B1G1 -->
                                <div class="mb-3 row">
                                    <label for="produk_ids_b1g1" class="col-lg-3 col-form-label">Pilih Produk</label>
                                    <div class="col-lg-9">
                                        <input type="hidden" name="produk_ids[]" id="selected_produk_b1g1">
                                        <div class="input-group">
                                            <input type="text" id="display_produk_b1g1" class="form-control" placeholder="Klik untuk memilih produk..." readonly>
                                            <button type="button" class="btn btn-info" onclick="openProdukModal('b1g1')">
                                                <i class="fa fa-search"></i> Pilih Produk
                                            </button>
                                        </div>
                                        <small class="text-muted">Produk terpilih: <span id="count_produk_b1g1">0</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buy A Get B Free -->
                        <div class="panel panel-default promo-details" id="buygetfree-details" style="display: none;">
                            <div class="panel-heading">
                                <h5 class="panel-title">Detail Buy A Get B Free</h5>
                            </div>
                            <div class="panel-body">
                                <div class="mb-3 row">
                                    <label for="buy_quantity" class="col-lg-3 col-form-label">Jumlah Beli</label>
                                    <div class="col-lg-9">
                                        <input type="number" name="buy_quantity" id="buy_quantity" class="form-control" min="1">
                                        <div class="invalid-feedback">Minimal pembelian untuk mendapat gratis</div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="get_quantity" class="col-lg-3 col-form-label">Jumlah Gratis</label>
                                    <div class="col-lg-9">
                                        <input type="number" name="get_quantity" id="get_quantity" class="form-control" min="1">
                                        <div class="invalid-feedback">Jumlah produk gratis yang didapat</div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_produk_buy" class="col-lg-3 col-form-label">Produk yang Dibeli</label>
                                    <div class="col-lg-9">
                                        <input type="hidden" name="id_produk_buy" id="id_produk_buy">
                                        <div class="input-group">
                                            <input type="text" id="display_produk_buy" class="form-control" placeholder="Klik untuk memilih produk yang dibeli..." readonly>
                                            <button type="button" class="btn btn-info" onclick="openProdukModal('buy')">
                                                <i class="fa fa-search"></i> Pilih Produk Beli
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Produk yang harus dibeli</div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_produk_get" class="col-lg-3 col-form-label">Produk Gratis</label>
                                    <div class="col-lg-9">
                                        <input type="hidden" name="id_produk_get" id="id_produk_get">
                                        <div class="input-group">
                                            <input type="text" id="display_produk_get" class="form-control" placeholder="Klik untuk memilih produk gratis..." readonly>
                                            <button type="button" class="btn btn-success" onclick="openProdukModal('get')">
                                                <i class="fa fa-search"></i> Pilih Produk Gratis
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Produk yang didapat gratis</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="start_date" class="col-lg-3 col-form-label">Tanggal Mulai</label>
                        <div class="col-lg-9">
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="end_date" class="col-lg-3 col-form-label">Tanggal Berakhir</label>
                        <div class="col-lg-9">
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-9 offset-lg-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" checked>
                                <label for="is_active" class="form-check-label">Aktif</label>
                            </div>
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

<!-- Modal untuk pemilihan produk -->
<div class="modal fade" id="modal-produk-selection" tabindex="-1" role="dialog" aria-labelledby="modal-produk-selection-title">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-produk-selection-title">Pilih Produk untuk Promo</h5>
                <button type="button" class="btn-close" id="closeModalProdukBtn" data-coreui-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Cari Produk:</label>
                    <input type="text" id="search-produk" class="form-control" placeholder="Ketik nama produk untuk mencari...">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="table-produk-selection" style="width: 100%;">
                        <thead class="table-primary">
                            <tr>
                                <th width="60px" class="text-center">
                                    <input type="checkbox" id="select-all-produk"> 
                                    <br><small>Pilih Semua</small>
                                </th>
                                <th width="120px">Kode</th>
                                <th width="*">Nama Produk</th>
                                <th width="150px">Kategori</th>
                                <th width="130px" class="text-right">Harga Jual</th>
                                <th width="80px" class="text-center">Stok</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fa fa-info-circle"></i> 
                    Produk terpilih: <strong><span id="selected-count">0</span></strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="acceptSelectedProduk()">
                    <i class="fa fa-check"></i> Accept (<span id="accept-count">0</span> Produk)
                </button>
                <button type="button" class="btn btn-secondary" id="closeModalProdukBtnFooter" data-coreui-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS khusus untuk modal produk -->
<style>
#modal-produk-selection .modal-dialog {
    margin: 1.5rem auto;
}

#modal-produk-selection .table th {
    background-color: #0d6efd;
    color: white;
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
    border-color: #0b5ed7;
}

#modal-produk-selection .table td {
    vertical-align: middle;
    padding: 0.5rem;
}

#modal-produk-selection .table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

#modal-produk-selection .table input[type="checkbox"] {
    transform: scale(1.2);
    margin: 0;
}

#modal-produk-selection .dataTables_wrapper .dataTables_length,
#modal-produk-selection .dataTables_wrapper .dataTables_info,
#modal-produk-selection .dataTables_wrapper .dataTables_paginate {
    margin-top: 0.75rem;
}

#modal-produk-selection .dataTables_wrapper .dataTables_filter {
    display: none; /* Hide karena kita punya search sendiri */
}

#modal-produk-selection .alert-info {
    margin-top: 1rem;
    font-size: 0.875rem;
    padding: 0.75rem 1rem;
}

.modal-xl {
    width: 90%;
    max-width: 1200px;
}

/* Form styling improvements */
.promo-details {
    margin-bottom: 1rem;
}

.promo-details .panel-body {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}

.form-group .invalid-feedback {
    color: #dc3545;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    margin-bottom: 0;
}

.input-group .btn {
    height: calc(1.5em + 0.75rem + 2px);
}

/* Make sure hidden fields don't interfere */
input[type="hidden"] {
    display: none !important;
}

@media (max-width: 768px) {
    .modal-xl {
        width: 95%;
        margin: 1rem auto;
    }
    
    #modal-produk-selection .modal-body {
        padding: 1rem;
    }
}
</style>

@push('scripts')
<script>
    let tableProdukSelection;
    let currentPromoType = '';
    let selectedProducts = [];

    $(document).ready(function() {
        // Initialize select2 hanya untuk single select (Buy A Get B)
        $('.select2-single').select2({
            dropdownParent: $('#modal-form'),
            width: '100%',
            placeholder: 'Pilih produk...',
            allowClear: true
        });

        // Initialize DataTable untuk modal produk selection
        tableProdukSelection = $('#table-produk-selection').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('produk.data') }}',
                data: function(d) {
                    d.search_value = $('#search-produk').val();
                },
                type: 'GET'
            },
            columns: [
                {
                    data: 'id_produk',
                    render: function(data, type, row) {
                        return '<div class="text-center"><input type="checkbox" class="produk-checkbox" value="' + data + '" data-nama="' + row.nama_produk + '"></div>';
                    },
                    orderable: false,
                    searchable: false,
                    width: '60px'
                },
                { 
                    data: 'kode_produk',
                    width: '120px'
                },
                { 
                    data: 'nama_produk',
                    render: function(data, type, row) {
                        return '<div style="word-wrap: break-word; max-width: 300px;">' + data + '</div>';
                    }
                },
                { 
                    data: 'nama_kategori',
                    defaultContent: '-',
                    width: '150px'
                },
                { 
                    data: 'harga_jual',
                    render: function(data) {
                        // Debug: console log untuk liat data yang masuk
                        console.log('Harga data:', data, typeof data);
                        // Pastikan data adalah number dan tampilkan format penuh
                        const price = parseFloat(data) || 0;
                        return '<div class="text-right">Rp ' + price.toLocaleString('id-ID') + '</div>';
                    },
                    width: '130px'
                },
                { 
                    data: 'stok',
                    render: function(data) {
                        return '<div class="text-center">' + data + '</div>';
                    },
                    width: '80px'
                }
            ],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
            dom: 'rtlip',
            order: [[1, 'asc']],
            scrollY: '350px',
            scrollCollapse: true,
            language: {
                processing: "Memuat...",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                search: "Cari:",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Search produk
        $('#search-produk').on('keyup', function() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(function() {
                tableProdukSelection.ajax.reload();
            }, 500); // Delay 500ms untuk menghindari request berlebihan
        });

        // Select all checkbox
        $('#select-all-produk').on('change', function() {
            $('.produk-checkbox').prop('checked', this.checked);
            updateSelectedCount();
        });

        // Individual checkbox
        $(document).on('change', '.produk-checkbox', function() {
            updateSelectedCount();
            
            // Update select all status
            let totalCheckboxes = $('.produk-checkbox').length;
            let checkedCheckboxes = $('.produk-checkbox:checked').length;
            
            $('#select-all-produk').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
            $('#select-all-produk').prop('checked', checkedCheckboxes === totalCheckboxes);
        });

        // Handle tipe promo change
        $('#tipe_promo').on('change', function() {
            var tipePromo = $(this).val();
            
            // Hide all details
            $('.promo-details').hide();
            
            // Clear form fields
            $('#discount_percentage, #buy_quantity, #get_quantity').val('');
            
            // Clear selected products
            clearSelectedProducts('percent');
            clearSelectedProducts('b1g1');
            clearSelectedProducts('buy');
            clearSelectedProducts('get');
            
            switch(tipePromo) {
                case 'percent_per_item':
                    $('#percent-details').show();
                    break;
                case 'b1g1_same_item':
                    $('#b1g1-details').show();
                    break;
                case 'buy_a_get_b_free':
                    $('#buygetfree-details').show();
                    break;
            }
        });

        // Set minimum date to today
        var today = new Date().toISOString().split('T')[0];
        $('#start_date').attr('min', today);
        
        $('#start_date').on('change', function() {
            $('#end_date').attr('min', $(this).val());
        });
    });

    function openProdukModal(type) {
        currentPromoType = type;
        $('#modal-produk-selection').modal('show');
        
        // Update modal title berdasarkan tipe
        if (type === 'buy') {
            $('#modal-produk-selection .modal-title').text('Pilih Produk yang Dibeli');
        } else if (type === 'get') {
            $('#modal-produk-selection .modal-title').text('Pilih Produk Gratis');
        } else {
            $('#modal-produk-selection .modal-title').text('Pilih Produk untuk Promo');
        }
        
        // Load existing selections
        loadExistingSelections(type);
        
        // Reload table
        tableProdukSelection.ajax.reload(function() {
            updateSelectedCount();
        });
    }

    function loadExistingSelections(type) {
        let existingIds = [];
        
        if (type === 'percent') {
            let value = $('#selected_produk_percent').val();
            existingIds = value ? value.split(',') : [];
        } else if (type === 'b1g1') {
            let value = $('#selected_produk_b1g1').val();
            existingIds = value ? value.split(',') : [];
        }
        
        // Mark checkboxes as checked after table loads
        setTimeout(function() {
            existingIds.forEach(function(id) {
                $('.produk-checkbox[value="' + id + '"]').prop('checked', true);
            });
            updateSelectedCount();
        }, 500);
    }

    function updateSelectedCount() {
        let count = $('.produk-checkbox:checked').length;
        $('#selected-count').text(count);
        $('#accept-count').text(count);
    }

    function acceptSelectedProduk() {
        let selectedIds = [];
        let selectedNames = [];
        
        $('.produk-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
            selectedNames.push($(this).data('nama'));
        });
        
        if (currentPromoType === 'percent') {
            $('#selected_produk_percent').val(selectedIds.join(','));
            $('#display_produk_percent').val(selectedNames.slice(0, 3).join(', ') + (selectedNames.length > 3 ? ` (+${selectedNames.length - 3} lainnya)` : ''));
            $('#count_produk_percent').text(selectedIds.length);
        } else if (currentPromoType === 'b1g1') {
            $('#selected_produk_b1g1').val(selectedIds.join(','));
            $('#display_produk_b1g1').val(selectedNames.slice(0, 3).join(', ') + (selectedNames.length > 3 ? ` (+${selectedNames.length - 3} lainnya)` : ''));
            $('#count_produk_b1g1').text(selectedIds.length);
        } else if (currentPromoType === 'buy') {
            // Untuk buy A get B, hanya bisa pilih 1 produk
            if (selectedIds.length > 1) {
                alert('Hanya bisa pilih 1 produk untuk "Produk yang Dibeli"');
                return;
            }
            if (selectedIds.length === 1) {
                $('#id_produk_buy').val(selectedIds[0]);
                $('#display_produk_buy').val(selectedNames[0]);
            }
        } else if (currentPromoType === 'get') {
            // Untuk buy A get B, hanya bisa pilih 1 produk
            if (selectedIds.length > 1) {
                alert('Hanya bisa pilih 1 produk untuk "Produk Gratis"');
                return;
            }
            if (selectedIds.length === 1) {
                $('#id_produk_get').val(selectedIds[0]);
                $('#display_produk_get').val(selectedNames[0]);
            }
        }
        
        $('#modal-produk-selection').modal('hide');
    }

    function clearSelectedProducts(type) {
        if (type === 'percent') {
            $('#selected_produk_percent').val('');
            $('#display_produk_percent').val('');
            $('#count_produk_percent').text('0');
        } else if (type === 'b1g1') {
            $('#selected_produk_b1g1').val('');
            $('#display_produk_b1g1').val('');
            $('#count_produk_b1g1').text('0');
        } else if (type === 'buy') {
            $('#id_produk_buy').val('');
            $('#display_produk_buy').val('');
        } else if (type === 'get') {
            $('#id_produk_get').val('');
            $('#display_produk_get').val('');
        }
    }

    // Handle modal close - clear selections if needed
    $('#modal-produk-selection').on('hidden.bs.modal', function() {
        $('.produk-checkbox').prop('checked', false);
        $('#select-all-produk').prop('checked', false).prop('indeterminate', false);
        updateSelectedCount();
    });
</script>
@endpush