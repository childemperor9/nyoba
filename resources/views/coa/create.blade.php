@extends('layouts.coreui') {{-- Menggunakan layout master CoreUI Anda --}}

@section('title', 'Tambah Data COA') {{-- Judul untuk halaman Tambah Data COA --}}

@section('content')
    <div class="container-fluid"> {{-- Menggunakan container-fluid CoreUI untuk lebar penuh --}}
        <div class="card">
            <div class="card-header">
                Tambah Data COA Baru
            </div>
            <div class="card-body">
                <form action="{{ route('coa.store') }}" method="POST">
                    @csrf {{-- Token CSRF untuk keamanan Laravel --}}

                    <div class="mb-3"> {{-- Menggunakan kelas margin-bottom Bootstrap --}}
                        <label for="acc_num" class="form-label">Kode COA :</label> {{-- Kelas form-label Bootstrap --}}
                        <input type="text" class="form-control @error('acc_num') is-invalid @enderror" id="acc_num" name="acc_num" value="{{ old('acc_num') }}" required>
                        @error('acc_num')
                            <div class="invalid-feedback">{{ $message }}</div> {{-- Kelas feedback validasi Bootstrap --}}
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="acc_name" class="form-label">Nama COA :</label>
                        <input type="text" class="form-control @error('acc_name') is-invalid @enderror" id="acc_name" name="acc_name" value="{{ old('acc_name') }}" required>
                        @error('acc_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4"> {{-- Menggunakan kelas flexbox Bootstrap --}}
                        <a href="{{ route('coa.index') }}" class="btn btn-secondary">Batal</a> {{-- Kelas btn Bootstrap --}}
                        <button type="submit" class="btn btn-primary">Simpan Data</button> {{-- Kelas btn Bootstrap --}}
                    </div>
                </form>
            </div> {{-- End card-body --}}
        </div> {{-- End card --}}
    </div> {{-- End container-fluid --}}
@endsection

{{-- Tidak perlu @push('styles') atau @push('scripts') di sini jika tidak ada yang spesifik --}}