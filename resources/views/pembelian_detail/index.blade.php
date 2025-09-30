@extends('layouts.coreui-master')

@section('title')
    Transaksi Pembelian
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

    .table-pembelian tbody tr:last-child {
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
    <li class="active">Transaksi Pembelian</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div class="box-header with-border" style="margin-bottom: 20px;">
                <table>
                    <tr>
                        <td>Supplier</td>
                        <td>: {{ $supplier->nama }}</td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>: {{ $supplier->telepon }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: {{ $supplier->alamat }}</td>
                    </tr>
                </table>
            </div>
            <div class="box-body">
                    
                <form class="form-produk">
                    @csrf
                    <div class="form-group row">
                        <label for="kode_produk" class="col-lg-2">Kode Produk</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_pembelian" id="id_pembelian" value="{{ $id_pembelian }}">
                                <input type="hidden" name="id_produk" id="id_produk">
                                <input type="text" class="form-control" name="kode_produk" id="kode_produk">
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-pembelian">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th width="15%">Jumlah</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar text-white bg-primary" style="border-radius: 10px; margin-bottom: 20px;"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-align">
                            <form action="{{ route('pembelian.store') }}" class="form-pembelian" method="post">
                                @csrf
                                <input type="hidden" name="id_pembelian" value="{{ $id_pembelian }}">
                                <input type="hidden" name="total" id="total">
                                <input type="hidden" name="total_item" id="total_item">
                                <input type="hidden" name="bayar" id="bayar">

                                <div class="form-group row">
                                    <label for="totalrp" class="col-lg-4 control-label">Total</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="totalrp" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="diskon" class="col-lg-4 control-label">Diskon</label>
                                    <div class="col-lg-8">
                                        <input type="number" name="diskon" id="diskon" class="form-control" value="{{ $diskon }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="bayar" class="col-lg-4 control-label">Bayar</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="bayarrp" class="form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 5px; text-align: right;">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

@includeIf('pembelian_detail.produk')
@endsection

@push('scripts')
<script>
    // Helper function for capitalizing words
    function ucwords(str) {
        return (str + '').replace(/^(.)|\s+(.)/g, function ($1) {
            return $1.toUpperCase();
        });
    }

    let table, table2;

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-pembelian').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('pembelian_detail.data', $id_pembelian) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_beli'},
                {data: 'jumlah'},
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
                loadForm($('#diskon').val());
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

            $.post(`{{ url('/pembelian_detail') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah
                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });

        $(document).on('input', '#diskon', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($(this).val());
        });

        $('.btn-simpan').on('click', function () {
            $('.form-pembelian').submit();
        });
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
        $.post('{{ route('pembelian_detail.store') }}', $('.form-produk').serialize())
            .done(response => {
                $('#kode_produk').focus();
                table.ajax.reload(() => {
                    loadForm($('#diskon').val());
                    // Add a small delay to ensure DOM is updated
                    setTimeout(() => {
                        // Trigger any additional updates if needed
                    }, 100);
                });
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
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload(() => loadForm($('#diskon').val()));
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function loadForm(diskon = 0) {
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
        
        // Debug values
        console.log('=== LOADFORM JS DEBUG ===', {
            diskon: diskon,
            totalValue: totalValue,
            totalParsed: parseFloat(totalValue),
            isValidTotal: !isNaN(parseFloat(totalValue))
        });
        
        // Handle case when total is zero - update display immediately
        if (totalValue === '0' || totalValue === '') {
            $('#totalrp').val('Rp. 0');
            $('#bayarrp').val('Rp. 0');
            $('#bayar').val('0');
            $('.tampil-bayar').text('Rp. 0');
            $('.tampil-terbilang').text('Nol Rupiah');
            return;
        }

        $.get(`{{ url('/pembelian_detail/loadform') }}/${diskon}/${totalValue}`)
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
                $('.tampil-bayar').text('Rp. '+ response.data.bayarrp);
                $('.tampil-terbilang').text(response.data.terbilang);
            })
            .fail(errors => {
                console.error('LoadForm AJAX error:', errors);
                console.error('Error details:', {
                    status: errors.status,
                    statusText: errors.statusText,
                    responseText: errors.responseText
                });
                
                // Fallback to manual calculation on error
                const totalParsed = parseFloat(totalValue) || 0;
                const bayar = totalParsed - (diskon / 100 * totalParsed);
                
                $('#totalrp').val('Rp. '+ totalParsed.toLocaleString('id-ID'));
                $('#bayarrp').val('Rp. '+ bayar.toLocaleString('id-ID'));
                $('#bayar').val(bayar);
                $('.tampil-bayar').text('Rp. '+ bayar.toLocaleString('id-ID'));
                $('.tampil-terbilang').text(ucwords(terbilang(bayar)+ ' Rupiah'));
                
                let errorMsg = 'Tidak dapat menampilkan data';
                if (errors.status === 404) {
                    errorMsg += ' (Route tidak ditemukan)';
                } else if (errors.status === 500) {
                    errorMsg += ' (Server error)';
                }
                
                alert(errorMsg);
                return;
            });
    }
</script>
@endpush