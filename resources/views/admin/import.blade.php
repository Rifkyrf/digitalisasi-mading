@extends('layouts.app')
@section('title', 'Import User dari Excel')

@section('content')
<div class="container py-4">
    <h2><i class="fas fa-file-excel"></i> Import Data User dari Excel</h2>
    <form action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Pilih File Excel</label>
            <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-upload"></i> Import
        </button>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection