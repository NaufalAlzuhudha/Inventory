@extends('layout.kai')
@section('page_title', $pageTitle)
@section('content')
    <div class="card py-5">
        <div class="card-body">
            <form class="row col-12 justify-content-between" id="form-add-produk">
                <div class="alert alert-danger" id="alert-danger"></div>
                <div class="row">
                    <div class="form-group w-25">
                        <label for="pengirim" class="form-label">Pengirim</label>
                        <input type="text" name="pengirim" id="pengirim" class="form-control">
                    </div>
                    <div class="form-group w-25">
                        <label for="kontak" class="form-label">Kontak</label>
                        <input type="text" name="kontak" id="kontak" class="form-control">
                    </div>
                    <div class="form-group mt-1">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control">
                    </div>
                </div>
                <div class="row mt-5 align-items-center">
                    <div class="col-4">
                        <select id="select-produk" class="form-select border" style="width: 100%;">
                        </select>
                    </div>
                    <div class="col-2">
                        <input type="text" name="nomor_batch" id="nomor_batch" class="form-control"
                            placeholder="Nomor Batch">
                    </div>
                    <div class="col-2">
                        <input type="number" name="qty" id="qty" class="form-control" placeholder="Qty">
                    </div>
                    <div class="col-2">
                        <input type="number" name="harga" id="harga" class="form-control" placeholder="Harga">
                    </div>
                    <div class="col-2">
                        <button type="submit" id="btn-add" class="btn btn-dark btn-round w-100">Tambah</button>
                    </div>
                </div>
            </form>
            <table class="table mt-5" id="table-produk">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 15px">No</th>
                        <th>Produk</th>
                        <th>Batch</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Sub Total</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-end">Grand Total</th>
                        <th id="grand_total">0</th>
                    </tr>
                    <tr>
                        <th colspan="7" class="text-end">
                            <form id="form-transaksi">
                                <button type="submit" class="btn btn-success btn-round">Simpan</button>
                            </form>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#alert-danger').hide();
            const numberFormat = new Intl.NumberFormat('id-ID');
            let selectedOption = {};
            let selectedProduk = [];

            $('#select-produk').select2({
                placeholder: 'Pilih produk...',
                allowClear: true,
                theme: 'bootstrap-5',
                width: '100%',
                ajax: {
                    url: '{{ route("get-data.varian-produk") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term || ''
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map((item) => {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    nomor_sku: item.nomor_sku,
                                    harga: item.harga,
                                    stok: item.stok
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#select-produk').on('select2:select', function (e) {
                selectedOption = e.params.data;
                if (selectedOption && selectedOption.harga) {
                    $('#harga').val(selectedOption.harga);
                }
            });

            $('#select-produk').on('select2:clear', function () {
                selectedOption = {};
                $('#harga').val('');
            });

            $("#form-add-produk").on("submit", function (e) {
                e.preventDefault();
                let qty = parseInt($("#qty").val());
                let harga = parseInt($("#harga").val());
                let nomor_batch = $("#nomor_batch").val();
                let produk_id = $("#select-produk").val();

                if (!selectedOption.id || !qty || !harga || !nomor_batch) {
                    swal({
                        title: "Perhatian",
                        text: "Input belum lengkap",
                        icon: "warning",
                        timer: 3000
                    })
                    return;
                }

                if (qty < 1 || harga < 1) {
                    swal({
                        title: "Perhatian",
                        text: "Qty dan harga harus lebih dari 0",
                        icon: "warning",
                        timer: 3000
                    })
                    return;
                }

                let subTotal = qty * harga;

                let existingItem = selectedProduk.find((item) => item.nomor_sku === selectedOption.nomor_sku);

                if (existingItem) {
                    existingItem.qty = parseInt(existingItem.qty) + parseInt(qty);
                    existingItem.harga = parseInt(harga);
                    existingItem.subTotal = existingItem.qty * existingItem.harga;
                } else {
                    selectedProduk.push({
                        text: selectedOption.text,
                        qty: qty,
                        harga: harga,
                        nomor_batch: nomor_batch,
                        nomor_sku: selectedOption.nomor_sku,
                        subTotal: subTotal
                    })
                }
                $('#select-produk').val(null).trigger('change');
                $('#qty').val('');
                $('#harga').val('');
                $('#nomor_batch').val('');
                renderTable();
            });

            function renderTable() {
                let tableBody = $('#table-produk tbody');
                tableBody.empty();

                if (selectedProduk.length === 0) {
                    tableBody.append(`<tr class="text-center"><td colspan="7">Tidak ada produk</td></tr>`);
                } else {
                    selectedProduk.forEach((item, index) => {
                        let row = `
                                                                                                                                <tr>
                                                                                                                                    <td class="text-center">${index + 1}</td>
                                                                                                                                    <td>${item.text}</td>
                                                                                                                                    <td>${item.nomor_batch}</td>
                                                                                                                                    <td>${item.qty}</td>
                                                                                                                                    <td>${numberFormat.format(item.harga)}</td>
                                                                                                                                    <td>${numberFormat.format(item.subTotal)}</td>
                                                                                                                                    <td>
                                                                                                                                        <button type="button" class="btn btn-danger btn-sm btn-round btn-delete-item" data-index="${index}" data-nomor-sku="${item.nomor_sku}">
                                                                                                                                            <i class="fa fa-trash" style="pointer-events: none;"></i>
                                                                                                                                        </button>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                            `;
                        tableBody.append(row);
                    });
                }

                let grandTotal = selectedProduk.reduce((total, item) => total + item.subTotal, 0);
                $('#grand_total').text(numberFormat.format(grandTotal));
            }

            $('#table-produk').on('click', '.btn-delete-item', function () {
                let index = $(this).data('index');
                selectedProduk.splice(index, 1);
                renderTable();
            });

            renderTable();

            $('#form-transaksi').on('submit', function (e) {
                e.preventDefault();
                if (selectedProduk.length === 0) {
                    swal({
                        title: "Perhatian",
                        text: "wajib menuliskan 1 produk yang akan dicatat",
                        icon: "warning",
                        timer: 3000
                    })
                    return;
                }

                $.ajax({
                    url: "{{ route('transaksi-masuk.store') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        items: selectedProduk,
                        pengirim: $('#pengirim').val(),
                        keterangan: $('#keterangan').val(),
                        kontak: $('#kontak').val(),
                    },
                    success: function (response) {
                        if (response.success) {
                            window.location.href = response.redirect_url;
                        }
                    },
                    error: function (xhr) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            renderErrors(errors);
                            return;
                        }
                        if (xhr.responseJSON?.message) {
                            swal({
                                title: "Error",
                                text: xhr.responseJSON.message,
                                icon: "error"
                            });
                        }
                    }
                })
            })

            function renderErrors(errors) {
                let alertBox = $('#alert-danger');
                alertBox.empty();
                Object.values(errors).forEach(err => {
                    err.forEach(msg => {
                        alertBox.append(`<p>${msg}</p>`);
                    })
                })
                alertBox.show();
            }
        });
    </script>
@endpush