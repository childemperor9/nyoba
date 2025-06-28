{{-- resources/views/konfigurasi/create.blade.php --}}

@extends('layouts.coreui') {{-- Pastikan layout master CoreUI Anda --}}

@section('title', 'Tambah Konfigurasi COA - Accounting')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0">Tambah Konfigurasi COA</span>
                    {{-- Navigasi kecil di kanan atas seperti di gambar --}}
                    <small>Konfigurasi COA / Tambah</small>
                </div>
            </div>
            <div class="card-body">
                {{-- Bagian Alert (Validation Errors) --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6>Ada kesalahan input:</h6>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="konfigurasiCoaForm" action="{{ route('konfigurasi.coa.store') }}" method="POST">
                    @csrf {{-- Token CSRF for security --}}

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Nama Konfigurasi COA <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Nama Konfigurasi COA" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="transaction_type" class="form-label">Master Data Jenis Transaksi</label>
                            <select class="form-select @error('transaction_type') is-invalid @enderror" id="transaction_type" name="transaction_type">
                                <option value="">Pilih ...</option>
                                {{-- Example options, you should populate this dynamically from your database if applicable --}}
                                <option value="Jurnal Manual" {{ old('transaction_type') == 'Jurnal Manual' ? 'selected' : '' }}>Jurnal Manual</option>
                                <option value="Pembelian" {{ old('transaction_type') == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                                <option value="Penjualan" {{ old('transaction_type') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                            </select>
                            @error('transaction_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="description" class="form-label">Keterangan</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" placeholder="Keterangan">
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <hr> {{-- Separator line --}}

                    <div class="mb-3">
                        <h6>Detail Akun Transaksi</h6>
                        <table class="table table-bordered" id="detail_table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Akun</th>
                                    <th style="width: 100px;">Persen</th>
                                    <th style="width: 100px;">D / K</th>
                                    <th>Nominal (untuk simulasi)</th>
                                    <th style="width: 50px;"></th> {{-- For delete button --}}
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Initial empty row --}}
                                <tr data-id="">
                                    <td></td> {{-- ID will be empty for new rows --}}
                                    <td>
                                        <select class="form-select coa_select" name="details[0][coa_id]" required>
                                            <option value="">Pilih Akun</option>
                                            {{-- Loop through all available COAs --}}
                                            @foreach ($allCoas as $coa)
                                                <option value="{{ $coa->id }}">({{ $coa->acc_num }}) {{ $coa->acc_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control text-end percentage_input" name="details[0][percentage]" value="100" min="0" max="100" required></td>
                                    <td>
                                        <select class="form-select type_select" name="details[0][type]" required>
                                            <option value="">Pilih Debet/Kredit</option>
                                            <option value="Debit">Debet</option>
                                            <option value="Kredit">Kredit</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control text-end nominal_input" name="details[0][nominal]" value="0" required></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-info btn-sm" id="add_row">Tambah Data</button>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-end fw-bold">Total Debet:</td>
                                    <td style="width: 150px;" class="text-end"><span id="total_debit">0</span></td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-bold">Total Kredit:</td>
                                    <td class="text-end"><span id="total_kredit">0</span></td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-bold text-danger">Perbedaan:</td>
                                    <td class="text-end text-danger"><span id="perbedaan">0</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
                        <button type="submit" name="save_and_back" value="1" class="btn btn-success">Simpan dan Kembali</button>
                    </div>
                </form>
            </div> {{-- End card-body --}}
        </div> {{-- End card --}}
    </div> {{-- End container-fluid --}}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detailTableBody = document.querySelector('#detail_table tbody');
        const addRowButton = document.getElementById('add_row');
        let rowIndex = 1; // Start from 1 for new rows, as initial row is index 0

        const totalDebitSpan = document.getElementById('total_debit');
        const totalKreditSpan = document.getElementById('total_kredit');
        const perbedaanSpan = document.getElementById('perbedaan');

        // Function to format numbers to currency format
        function formatRupiah(angka) {
            if (isNaN(angka)) return '0';
            const reverse = angka.toString().split('').reverse().join('');
            const ribuan = reverse.match(/\d{1,3}/g);
            const hasil = ribuan.join('.').split('').reverse().join('');
            return hasil;
        }

        // Function to recalculate total debit, credit, and difference
        function calculateTotals() {
            let totalDebit = 0;
            let totalKredit = 0;

            detailTableBody.querySelectorAll('tr').forEach(row => {
                const typeSelect = row.querySelector('.type_select');
                const nominalInput = row.querySelector('.nominal_input');

                if (typeSelect && nominalInput) {
                    const nominalValue = parseFloat(nominalInput.value.replace(/\./g, '') || 0); // Remove thousand separators
                    const type = typeSelect.value;

                    if (type === 'Debit') {
                        totalDebit += nominalValue;
                    } else if (type === 'Kredit') {
                        totalKredit += nominalValue;
                    }
                }
            });

            const perbedaan = totalDebit - totalKredit;

            totalDebitSpan.textContent = formatRupiah(totalDebit);
            totalKreditSpan.textContent = formatRupiah(totalKredit);
            perbedaanSpan.textContent = formatRupiah(perbedaan);

            if (perbedaan === 0) {
                perbedaanSpan.classList.remove('text-danger');
            } else {
                perbedaanSpan.classList.add('text-danger');
            }
        }

        // Function to add a new row
        addRowButton.addEventListener('click', function () {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td></td> {{-- ID will be empty for new rows --}}
                <td>
                    <select class="form-select coa_select" name="details[${rowIndex}][coa_id]" required>
                        <option value="">Pilih Akun</option>
                        @foreach ($allCoas as $coa)
                            <option value="{{ $coa->id }}">({{ $coa->acc_num }}) {{ $coa->acc_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" class="form-control text-end percentage_input" name="details[${rowIndex}][percentage]" value="100" min="0" max="100" required></td>
                <td>
                    <select class="form-select type_select" name="details[${rowIndex}][type]" required>
                        <option value="">Pilih Debet/Kredit</option>
                        <option value="Debit">Debet</option>
                        <option value="Kredit">Kredit</option>
                    </select>
                </td>
                <td><input type="text" class="form-control text-end nominal_input" name="details[${rowIndex}][nominal]" value="0" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                </td>
            `;
            detailTableBody.appendChild(newRow);
            rowIndex++;
            attachRowListeners(newRow); // Attach event listeners to the new row
            calculateTotals(); // Recalculate totals after adding a row
        });

        // Event delegation for delete button and input/select within the table
        detailTableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                // Ensure there's at least 1 row
                if (detailTableBody.children.length > 1) {
                    e.target.closest('tr').remove();
                    calculateTotals(); // Recalculate totals after removing a row
                } else {
                    alert('Minimal harus ada satu baris detail.');
                }
            }
        });

        // Event delegation for nominal and type (Debit/Credit) changes
        function attachRowListeners(row) {
            const nominalInput = row.querySelector('.nominal_input');
            const typeSelect = row.querySelector('.type_select');

            // Event listener for nominal format while typing
            if (nominalInput) {
                nominalInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, ''); // Remove all non-digits
                    this.value = formatRupiah(value);
                    calculateTotals();
                });
                nominalInput.addEventListener('blur', function() {
                    // Ensure there's a value, if empty set to 0
                    if (this.value.trim() === '') {
                        this.value = formatRupiah(0);
                    }
                    calculateTotals();
                });
            }

            // Event listener for Debit/Credit type changes
            if (typeSelect) {
                typeSelect.addEventListener('change', calculateTotals);
            }
        }

        // Attach event listeners to all existing rows when the page loads
        detailTableBody.querySelectorAll('tr').forEach(attachRowListeners);

        // Calculate totals when the page first loads
        calculateTotals();
    });
</script>
@endpush
