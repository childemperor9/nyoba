@extends('layouts.coreui') {{-- Menggunakan layout master CoreUI Anda --}}

@section('title', 'Edit Data COA') {{-- Judul untuk halaman Edit Data COA --}}

@section('content')
    <div class="container-fluid"> {{-- Menggunakan container-fluid CoreUI untuk lebar penuh --}}
        <div class="card">
            <div class="card-header">
                Edit Data COA
            </div>
            <div class="card-body">
                <form action="{{ route('coa.update', $coa->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Penting untuk metode HTTP PUT --}}

                    <div class="mb-3">
                        <label for="acc_num" class="form-label">Kode COA:</label>
                        {{-- Gunakan old() untuk mempertahankan input jika ada error validasi, fallback ke data COA --}}
                        <input type="text" class="form-control @error('acc_num') is-invalid @enderror" id="acc_num" name="acc_num" value="{{ old('acc_num', $coa->acc_num) }}" required>
                        @error('acc_num')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="acc_name" class="form-label">Nama COA:</label>
                        <input type="text" class="form-control @error('acc_name') is-invalid @enderror" id="acc_name" name="acc_name" value="{{ old('acc_name', $coa->acc_name) }}" required>
                        @error('acc_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('coa.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Perbarui Data</button>
                    </div>
                </form>
            </div> {{-- End card-body --}}
        </div> {{-- End card --}}
    </div> {{-- End container-fluid --}}
@endsection

{{-- Tidak perlu @push('styles') atau @push('scripts') di sini jika tidak ada yang spesifik --}}