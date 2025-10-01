@extends('layouts.coreui-master')

@section('title')
    Transaksi Penjualan
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
        border-radius: 10px; /* Rounded corners */
        margin-bottom: 20px; /* Line spacing */
    }

    .table-penjualan tbody tr:last-child {
        display: none;
    }

    /* Add line spacing and rounded corners to form elements */
    .form-group {
        margin-bottom: 20px; /* Line spacing */
    }
    
    .form-control, .btn {
        border-radius: 10px; /* All radius 10px */
    }
    
    .input-group .form-control, 
    .input-group .btn {
        border-radius: 0;
    }
    
    .input-group .form-control:first-child,
    .input-group .btn:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .input-group .form-control:last-child,
    .input-group .btn:last-child {
        border-radius: 0 10px 10px 0;
    }
    
    .input-group .btn {
        border-radius: 0 10px 10px 0;
    }
    
    .radio label {
        margin-right: 20px;
        padding: 8px 15px;
        border-radius: 10px; /* All radius 10px */
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    
    .radio input[type="radio"] {
        margin-right: 5px;
    }
    
    .table {
        border-radius: 10px; /* All radius 10px */
        overflow: hidden;
        margin-bottom: 20px; /* Line spacing */
    }
    
    .box {
        border-radius: 10px !important; /* All radius 10px */
        margin-bottom: 20px; /* Line spacing */
    }
    
    .box-footer {
        border-radius: 0 0 10px 10px !important;
        padding: 20px;
        background-color: #f8f9fa;
    }
    
    .btn-simpan {
        border-radius: 10px; /* All radius 10px */
        padding: 10px 20px;
    }

    /* Align form widths */
    .form-align {
        max-width: 600px; /* Align form lengths */
        margin: 0 auto;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
        
        .form-align {
            max-width: 100%;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Penjualan</li>
@endsection

@section('content')
<!-- Error Messages -->
@if ($errors->has('qris_error'))
<div class="alert alert-danger alert-dismissible" style="border-radius: 10px;">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> QRIS Payment Error!</h4>
    {{ $errors->first('qris_error') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible" style="border-radius: 10px;">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
</div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="box" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div class="box-body">
                <!-- Product Input Section -->
                <div style="margin-bottom: 30px;">
                    <form class="form-produk">
                    @csrf
                    <div class="form-group row">
                        <label for="kode_produk" class="col-lg-2">Kode Produk</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_penjualan" id="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="id_produk" id="id_produk">
                                <input type="text" id="barcode-input" class="form-control" autofocus placeholder="Scan barcode" style="border-radius: 10px 0 0 10px;">
                                <input type="hidden" class="form-control" name="kode_produk" id="kode_produk">
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn-trans btn-main" type="button" style="border-radius: 0 10px 10px 0;"><i class="mynaui-arrow-right"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th width="15%">Jumlah</th>
                        <th>Promo</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar text-white bg-main" style="border-radius: 10px; margin-bottom: 20px;"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-align">
                            <form action="{{ route('transaksi.simpan') }}" class="form-penjualan" method="post">
                                @csrf
                                <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="total" id="total">
                                <input type="hidden" name="total_item" id="total_item">
                                <input type="hidden" name="bayar" id="bayar">
                                <input type="hidden" name="diskon" value="0"> <!-- Always 0, promo handles discounts -->
                                <input type="hidden" name="id_member" id="id_member" value="{{ $memberSelected->id_member }}">

                                <div class="form-group row">
                                    <label for="totalrp" class="col-lg-4 control-label">Total</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="totalrp" class="form-control" readonly>
                                    </div>
                                </div>
                                
                                <!-- Payment Method Selection -->
                                <div class="form-group row">
                                    <label class="col-lg-4 control-label">Metode Bayar</label>
                                    <div class="col-lg-8">
                                        <div class="radio" style="margin-top: 7px;">
                                            <label style="margin-right: 20px;">
                                                <input type="radio" name="metode_pembayaran" value="CASH" checked>
                                                <i class="fa fa-money" style="margin-right: 5px;"></i> Cash
                                            </label>
                                            <label>
                                                <input type="radio" name="metode_pembayaran" value="QRIS">
                                                <i class="fa fa-qrcode" style="margin-right: 5px;"></i> QRIS
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="kode_member" class="col-lg-4 control-label">Member</label>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="kode_member" value="{{ $memberSelected->kode_member }}" style="border-radius: 10px 0 0 10px;">
                                            <span class="input-group-btn">
                                                <button onclick="tampilMember()" class="btn-trans btn-main" type="button" style="border-radius: 0 10px 10px 0;"><i class="mynaui-arrow-right"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="bayar" class="col-lg-4 control-label">Bayar</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="bayarrp" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="diterima" class="col-lg-4 control-label">Diterima</label>
                                    <div class="col-lg-8">
                                        <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $penjualan->diterima ?? 0 }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="kembali" class="col-lg-4 control-label">Kembali</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="kembali" name="kembali" class="form-control" value="0" readonly>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

                <div style="margin-top: 5px; text-align: right;">
                    <button type="submit" class="btn-with-icon btn-main btn-simpan"><i class="mynaui-save"></i> Simpan Transaksi</button>
                </div>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan_detail.produk')
@includeIf('penjualan_detail.member')
@endsection

@push('scripts')
<script>
    // Helper function for capitalizing words
    function ucwords(str) {
        return (str + '').replace(/^(.)|\s+(.)/g, function ($1) {
            return $1.toUpperCase();
        });
    }
    
    // Helper function for terbilang (using existing PHP function through AJAX or fallback)
    function terbilang(number) {
        // For now, we'll use a simple fallback
        // In a production environment, you might want to make an AJAX call to get this from the server
        return '...'; // Simplified fallback
    }

    let table, table2;

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('transaksi.data', $id_penjualan) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'promo'},
                {data: 'subtotal'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            // Add delay to ensure DOM is updated
            setTimeout(() => {
                loadForm(0); // No manual discount, always 0
                setTimeout(() => {
                    $('#diterima').trigger('input');
                    // Also trigger payment method update to refresh display
                    $('input[name="metode_pembayaran"]:checked').trigger('change');
                }, 300);
            }, 100);
        });
        table2 = $('.table-produk').DataTable();

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let jumlah = parseInt($(this).val());

            if (jumlah < 1) {
                $(this).val(1);
                alert('Jumlah tidak boleh kurang dari 1');
                return;
            }
            if (jumlah > 10000) {
                $(this).val(10000);
                alert('Jumlah tidak boleh lebih dari 10000');
                return;
            }

            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah
                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload(() => loadForm(0)); // No discount, always 0
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });

        $('#diterima').on('input', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm(0, $(this).val()); // No manual discount
        }).focus(function () {
            $(this).select();
        });

        $('.btn-simpan').on('click', function () {
            $('.form-penjualan').submit();
        });
        
        // Handle payment method change
        $('input[name="metode_pembayaran"]').on('change', function() {
            const paymentMethod = $(this).val();
            
            if (paymentMethod === 'QRIS') {
                // QRIS mode: disable diterima input and set to total amount
                const totalValue = $('.total').text().trim() || '0';
                $('#diterima').prop('disabled', true).val(totalValue);
                
                // Get the display value safely with better fallback logic
                let displayValue = '0';
                const bayarRpValue = $('#bayarrp').val();
                
                if (bayarRpValue && bayarRpValue !== 'Rp. undefined' && bayarRpValue.includes('Rp.')) {
                    displayValue = bayarRpValue.replace('Rp. ', '');
                } else if (totalValue && totalValue !== '0' && !isNaN(parseFloat(totalValue))) {
                    // Fallback to total value if bayarrp is not yet populated or has undefined
                    displayValue = parseFloat(totalValue).toLocaleString('id-ID');
                }
                
                $('.tampil-bayar').text('Total QRIS: Rp. ' + displayValue);
                $('.tampil-terbilang').text('Pembayaran akan dilakukan via QRIS');
                $('#kembali').val('Rp. 0');
            } else {
                // Cash mode: enable diterima input
                $('#diterima').prop('disabled', false).val(0);
                // Add delay to ensure DOM is ready
                setTimeout(() => {
                    loadForm(0, 0); // Reset to cash calculation
                }, 200);
            }
        });
        
        // Initialize payment method on page load
        setTimeout(() => {
            $('input[name="metode_pembayaran"]:checked').trigger('change');
        }, 300);
    });

    function tampilProduk() {
        $('#modal-produk').modal('show');
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function pilihProduk(id, kode) {
        $('#id_produk').val(id);
        $('#kode_produk').val(kode);
        hideProduk();
        tambahProduk();
    }

    function tambahProduk() {
        $.post('{{ route('transaksi.store') }}', $('.form-produk').serialize())
            .done(response => {
                // Handle both string and object response
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        // If not JSON, treat as simple success
                        $('#kode_produk').focus();
                        table.ajax.reload(() => {
                            loadForm(0); // No manual discount
                            // Trigger payment method update to refresh display
                            $('input[name="metode_pembayaran"]:checked').trigger('change');
                        });
                        return;
                    }
                }
                
                if (response.status !== 'error') {
                    $('#kode_produk').focus();
                    
                    // Jika ada free items, beri delay lebih lama untuk database consistency
                    const reloadDelay = response.data && response.data.has_free_items ? 200 : 50;
                    
                    setTimeout(() => {
                        table.ajax.reload(() => {
                            loadForm(0); // No manual discount
                            
                            // Trigger payment method update to refresh display
                            setTimeout(() => {
                                $('input[name="metode_pembayaran"]:checked').trigger('change');
                            }, 100);
                            
                            // Tampilkan notifikasi promo jika ada
                            if (response.data && response.data.promo_applied && response.data.promo_messages && response.data.promo_messages.length > 0) {
                                let promoMessage = 'Promo diterapkan:\n' + response.data.promo_messages.join('\n');
                                
                                // Show notification without blocking
                                setTimeout(() => {
                                    alert(promoMessage);
                                }, 100);
                            }
                            
                            // Tampilkan saran promo (non-blocking)
                            if (response.data && response.data.has_suggestions && response.data.promo_suggestions && response.data.promo_suggestions.length > 0) {
                                let suggestionMessage = 'Info Promo Tersedia:\n' + response.data.promo_suggestions.join('\n');
                                
                                // Show as a subtle notification (could be replaced with toast notification)
                                setTimeout(() => {
                                    if (confirm(suggestionMessage + '\n\nMau ditambahkan?')) {
                                        // Could add logic here to automatically add required items
                                        // For now, just show the suggestion
                                    }
                                }, 200);
                            }
                        });
                    }, reloadDelay);
                    
                } else {
                    alert(response.message || 'Tidak dapat menyimpan data');
                }
            })
            .fail(errors => {
                console.error('AJAX Error:', errors);
                let errorMessage = 'Tidak dapat menyimpan data';
                
                if (errors.responseJSON && errors.responseJSON.message) {
                    errorMessage = errors.responseJSON.message;
                } else if (errors.responseText) {
                    try {
                        const errorData = JSON.parse(errors.responseText);
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {
                        // Keep default message
                    }
                }
                
                alert(errorMessage);
            });
    }

    function tampilMember() {
        $('#modal-member').modal('show');
    }

    function pilihMember(id, kode) {
        $('#id_member').val(id);
        $('#kode_member').val(kode);
        loadForm(0); // No manual discount
        $('#diterima').val(0).select();
        hideMember();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload(() => loadForm(0)); // No manual discount
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function loadForm(diskon = 0, diterima = 0) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());
        
        // Get and validate total value
        let totalValue = $('.total').text().trim();
        
        // Handle empty or non-numeric total
        if (!totalValue || totalValue === '' || isNaN(totalValue)) {
            console.warn('Invalid total value:', totalValue);
            totalValue = '0';
        }
        
        // Ensure parameters are numeric
        diskon = parseFloat(diskon) || 0;
        diterima = parseFloat(diterima) || 0;
        
        // Debug values
        console.log('=== LOADFORM JS DEBUG ===', {
            diskon: diskon,
            diterima: diterima,
            totalValue: totalValue,
            totalParsed: parseFloat(totalValue),
            isValidTotal: !isNaN(parseFloat(totalValue))
        });
        
        // Build URL with safe parameters
        const url = `{{ url('/transaksi/loadform') }}/${diskon}/${totalValue}/${diterima}`;
        console.log('LoadForm URL:', url);
        
        // Handle case when total is zero - update display immediately
        if (totalValue === '0' || totalValue === '') {
            $('#totalrp').val('Rp. 0');
            $('#bayarrp').val('Rp. 0');
            $('#bayar').val('0');
            
            // Update display based on payment method
            const paymentMethod = $('input[name="metode_pembayaran"]:checked').val() || 'CASH';
            if (paymentMethod === 'QRIS') {
                $('.tampil-bayar').text('Total QRIS: Rp. 0');
                $('.tampil-terbilang').text('Pembayaran akan dilakukan via QRIS');
            } else {
                $('.tampil-bayar').text('Bayar: Rp. 0');
                $('.tampil-terbilang').text('Nol Rupiah');
            }
            
            $('#kembali').val('Rp. 0');
            return;
        }

        $.get(url)
            .done(response => {
                console.log('LoadForm response:', response);
                if (response.status === 'error') {
                    console.error('Backend error:', response.message);
                    alert('Error: ' + response.message);
                    return;
                }
                
                // Update form fields
                $('#totalrp').val('Rp. '+ response.data.totalrp);
                $('#bayarrp').val('Rp. '+ response.data.bayarrp);
                $('#bayar').val(response.data.bayar);
                
                // Update display based on payment method
                const paymentMethod = $('input[name="metode_pembayaran"]:checked').val() || 'CASH';
                if (paymentMethod === 'QRIS') {
                    // For QRIS, display the payment amount clearly
                    $('.tampil-bayar').text('Total QRIS: Rp. '+ response.data.bayarrp.replace('Rp. ', ''));
                    $('.tampil-terbilang').text('Pembayaran akan dilakukan via QRIS');
                } else {
                    $('.tampil-bayar').text('Bayar: Rp. '+ response.data.bayarrp);
                    $('.tampil-terbilang').text(response.data.terbilang);
                }

                $('#kembali').val('Rp. '+ response.data.kembalirp);
                if (diterima != 0 && paymentMethod !== 'QRIS') {
                    $('.tampil-bayar').text('Kembali: Rp. '+ response.data.kembalirp);
                    $('.tampil-terbilang').text(response.data.kembali_terbilang);
                }
            })
            .fail(errors => {
                console.error('LoadForm AJAX error:', errors);
                console.error('Error details:', {
                    status: errors.status,
                    statusText: errors.statusText,
                    responseText: errors.responseText,
                    url: url
                });
                
                // Fallback to manual calculation on error
                const totalParsed = parseFloat(totalValue) || 0;
                const bayar = totalParsed - (diskon / 100 * totalParsed);
                const kembali = (diterima != 0) ? diterima - bayar : 0;
                
                $('#totalrp').val('Rp. '+ totalParsed.toLocaleString('id-ID'));
                $('#bayarrp').val('Rp. '+ bayar.toLocaleString('id-ID'));
                $('#bayar').val(bayar);
                
                const paymentMethod = $('input[name="metode_pembayaran"]:checked').val() || 'CASH';
                if (paymentMethod === 'QRIS') {
                    $('.tampil-bayar').text('Total QRIS: Rp. '+ bayar.toLocaleString('id-ID'));
                    $('.tampil-terbilang').text('Pembayaran akan dilakukan via QRIS');
                } else {
                    $('.tampil-bayar').text('Bayar: Rp. '+ bayar.toLocaleString('id-ID'));
                    $('.tampil-terbilang').text(ucwords(terbilang(bayar)+ ' Rupiah'));
                }
                
                $('#kembali').val('Rp. '+ kembali.toLocaleString('id-ID'));
                
                let errorMsg = 'Tidak dapat menampilkan data';
                if (errors.status === 404) {
                    errorMsg += ' (Route tidak ditemukan - URL: ' + url + ')';
                } else if (errors.status === 500) {
                    errorMsg += ' (Server error)';
                }
                
                alert(errorMsg);
                return;
            });
    }
    $(function () {
    // Fokus otomatis ke input barcode
    $('#barcode-input').focus();

    // Event listener untuk barcode scanner
    $(document).on('keydown', '#barcode-input', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const barcode = $(this).val().trim();
                
                if (barcode) {
                    $.ajax({
                        url: '{{ route('transaksi.cariProduk') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            barcode: barcode
                        },
                        success: function(produk) {
                            if (produk) {
                                $('#id_produk').val(produk.id_produk);
                                $('#kode_produk').val(produk.kode_produk);
                                
                                // Call tambahProduk which will handle promo notifications
                                tambahProduk();
                                
                                // Langsung siap untuk scan produk berikutnya
                                $('#barcode-input').val('').focus();
                            } else {
                                alert('Produk tidak ditemukan');
                                $('#barcode-input').val('').focus();
                            }
                        },
                        error: function() {
                            alert('Gagal mencari produk');
                            $('#barcode-input').val('').focus();
                        }
                    });
                }
            }
        });
    });
    // Event listener untuk input uang diterima
    $(document).on('keydown', '#diterima', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('.btn-simpan').click(); // panggil tombol simpan
        }
    });
</script>
@endpush