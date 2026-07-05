@extends('layouts.app')
@section('title', 'Tambah Penduduk')

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title">Tambah Data Penduduk</h3></div>
    <form action="{{ route('penduduk.store') }}" method="POST">
        <div class="card-body">@include('penduduk._form')</div>
        <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('penduduk.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
