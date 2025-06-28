@extends('layouts.coreui') {{-- Menggunakan layout master CoreUI Anda --}}

@section('title', 'Master COA - Accounting') {{-- Judul untuk halaman daftar COA --}}

@section('content')
    <div class="container-fluid"> {{-- Menggunakan container-fluid CoreUI untuk lebar penuh --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0">Master COA</span>
                    <a href="{{ route('coa.create') }}" class="btn btn-success btn-sm">Tambah Data COA</a> {{-- Tambahkan btn-sm untuk ukuran kecil --}}
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

                {{-- Tabel Data COA --}}
                @if (empty($allCoas) || $allCoas->isEmpty())
                    <p class="text-center text-secondary mt-3">Tidak ada data COA yang ditemukan.</p>
                @else
                    <div class="table-responsive"> {{-- Kelas Bootstrap untuk tabel responsif --}}
                        <table class="table table-bordered table-hover mt-3"> {{-- Kelas tabel Bootstrap/CoreUI --}}
                            <thead>
                                <tr>
                                    <th>Akun COA</th>
                                    <th style="width: 150px;s text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allCoas as $coa)
                                    @php
                                        $currentAccNum = $coa->acc_num;
                                        $accName = $coa->acc_name;

                                        $parts = explode('.', $currentAccNum);
                                        $part1 = $parts[0] ?? '';
                                        $part2 = $parts[1] ?? '';
                                        $part3 = $parts[2] ?? '';

                                        $cellCssClass = '';

                                        // Sesuaikan indentasi dengan utility class Bootstrap `ps-` (padding-start)
                                        if ($part2 === '00' && $part3 === '000') {
                                            if (strlen($part1) === 1) {
                                                $cellCssClass = 'ps-1'; // Padding start
                                            } elseif (substr($part1, -2) === '00') {
                                                $cellCssClass = 'ps-3'; // Padding start, indent level 1
                                            } else {
                                                $cellCssClass = 'ps-4'; // Indent level 2
                                            }
                                        } elseif ($part3 === '000') {
                                            $cellCssClass = 'ps-5'; // Indent level 3
                                        } else {
                                            $cellCssClass = 'ps-6'; // Indent level 4
                                        }
                                    @endphp

                                    <tr>
                                        <td class="{{ $cellCssClass }}">
                                            {{ $currentAccNum }} {{ $accName }}
                                        </td>
                                        <td class="text-center">
                                            {{-- Menggunakan Dropdown Bootstrap/CoreUI --}}
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton{{ $coa->id }}" data-coreui-toggle="dropdown" aria-expanded="false">
                                                    Aksi
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $coa->id }}">
                                                    <li><a class="dropdown-item" href="{{ route('coa.create') }}">Tambah Data</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('coa.edit', $coa->id) }}">Edit</a></li>
                                                    <li>
                                                        <form action="{{ route('coa.destroy', $coa->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data COA {{ $coa->acc_num }} - {{ $coa->acc_name }}?');">Hapus</button>
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
                @endif
            </div> {{-- End card-body --}}
        </div> {{-- End card --}}
    </div> {{-- End container-fluid --}}
@endsection

@push('styles')
    <style>
        /* CSS kustom untuk indentasi, jika ps-x belum cukup */
        .ps-1 { padding-left: 0.25rem !important; } /* .25 * 16px = 4px */
        .ps-3 { padding-left: 1rem !important; }   /* 1 * 16px = 16px */
        .ps-4 { padding-left: 1.5rem !important; } /* 1.5 * 16px = 24px */
        .ps-5 { padding-left: 2rem !important; }   /* 2 * 16px = 32px */
        .ps-6 { padding-left: 2.5rem !important; } /* 2.5 * 16px = 40px */
        /* Anda bisa sesuaikan nilai rem sesuai kebutuhan visual */

        /* Styling khusus untuk tombol hapus di dropdown */
        .dropdown-item.text-danger:hover {
            background-color: var(--cui-danger-bg-subtle) !important; /* Contoh hover background */
        }
        /* Jika tombol hapus tidak terlihat seperti link saat di dropdown */
        .dropdown-item button.text-danger {
            background: none;
            border: none;
            color: inherit; /* Ambil warna dari .dropdown-item.text-danger */
            padding: 0;
            text-align: inherit;
            width: 100%;
            cursor: pointer;
            display: block; /* Agar mengisi seluruh lebar item dropdown */
        }
        .dropdown-item button.text-danger:hover {
            /* Pastikan hover state sama dengan .dropdown-item biasa */
            background-color: var(--cui-dropdown-hover-bg, rgba(0, 0, 0, 0.075));
        }
    </style>
@endpush

{{-- Tidak perlu JavaScript kustom untuk dropdown di sini karena CoreUI/Bootstrap sudah menanganinya --}}
{{-- Jika ada JS spesifik lainnya untuk halaman COA ini, bisa tambahkan di @push('scripts') --}}