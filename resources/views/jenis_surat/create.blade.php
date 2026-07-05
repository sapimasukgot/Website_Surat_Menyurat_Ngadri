@extends('layouts.app')
@section('title', 'Tambah Jenis Surat')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title">Tambah Jenis Surat</h3></div>
    <form action="{{ route('jenis-surat.store') }}" method="POST" enctype="multipart/form-data">
        <div class="card-body">@include('jenis_surat._form')</div>
        <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('jenis-surat.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
