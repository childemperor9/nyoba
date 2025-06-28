{{-- resources/views/konfigurasi/konfigurasicoa.blade.php --}}

@extends('layouts.coreui')

@section('title', 'Konfigurasi COA - Accounting')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0">Konfigurasi COA</span>
                    <a href="{{ route('konfigurasi.coa.create') }}" class="btn btn-success btn-sm">Tambah Data</a>
                </div>
            </div>
            <div class="card-body">
                {{-- Bagian Alert (Success/Error/Validation) --}}
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

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Bagian Pencarian dan Per Halaman --}}
                <div class="row mb-3">
                    <div class="col-md-6 d-flex align-items-center">
                        <label for="search" class="me-2 mb-0">Cari:</label>
                        {{-- Form untuk pencarian --}}
                        <form action="{{ route('konfigurasi.coa.index') }}" method="GET" class="d-flex w-100">
                            <input type="text" id="search" name="search" class="form-control form-control-sm me-2" placeholder="Ketik Untuk Mencari" value="{{ request('search') }}">
                            {{-- <button type="submit" class="btn btn-primary btn-sm">Cari</button> --}} {{-- Tombol cari opsional --}}
                        </form>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-end">
                        <label for="perPage" class="me-2 mb-0">Per Halaman:</label>
                        <select id="perPage" class="form-select form-select-sm" style="width: auto;" onchange="window.location.href = '{{ route('konfigurasi.coa.index') }}?per_page=' + this.value + '&search={{ request('search') }}'">
                            <option value="5" @selected(request('per_page', 5) == 5)>5</option>
                            <option value="10" @selected(request('per_page') == 10)>10</option>
                            <option value="25" @selected(request('per_page') == 25)>25</option>
                            <option value="50" @selected(request('per_page') == 50)>50</option>
                            <option value="100" @selected(request('per_page') == 100)>100</option>
                        </select>
                    </div>
                </div>


                {{-- Tabel Data Konfigurasi COA --}}
                @if ($configurations->isEmpty())
                    <p class="text-center text-secondary mt-3">Tidak ada data konfigurasi COA yang ditemukan.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Keterangan</th>
                                    <th style="width: 150px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($configurations as $config)
                                    <tr>
                                        <td>{{ $config->name }}</td>
                                        <td>{{ $config->description }}</td> {{-- Sesuaikan dengan nama kolom 'description' --}}
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-danger dropdown-toggle" type="button" id="dropdownMenuButton{{ $config->id }}" data-coreui-toggle="dropdown" aria-expanded="false">
                                                    Aksi
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $config->id }}">
                                                    <li><a class="dropdown-item" href="{{ route('konfigurasi.coa.edit', $config->id) }}">Edit</a></li>
                                                    <li>
                                                        <form action="{{ route('konfigurasi.coa.destroy', $config->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data konfigurasi {{ $config->name }}?');">Hapus</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Bagian Paginasi --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        {{-- UBAH INI DARI 'pagination::bootstrap-5' MENJADI 'pagination::bootstrap-4' --}}
                        {{ $configurations->links('pagination::bootstrap-4') }}
                        <p class="mb-0 text-end">{{ $configurations->total() }} Total Items</p>
                    </div>

                @endif
            </div> {{-- End card-body --}}
        </div> {{-- End card --}}
    </div> {{-- End container-fluid --}}
@endsection

@push('styles')
    <style>
        /* Custom CSS for dropdown button width */
        .dropdown .btn {
            width: 80px; /* Adjust as needed */
        }
        /* Styling khusus untuk tombol hapus di dropdown */
        .dropdown-item.text-danger:hover {
            background-color: var(--cui-danger-bg-subtle) !important; /* Contoh hover background */
        }
        .dropdown-item button.text-danger {
            background: none;
            border: none;
            color: inherit;
            padding: 0;
            text-align: inherit;
            width: 100%;
            cursor: pointer;
            display: block;
        }
        .dropdown-item button.text-danger:hover {
            background-color: var(--cui-dropdown-hover-bg, rgba(0, 0, 0, 0.075));
        }
    </style>
@endpush