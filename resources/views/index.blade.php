@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Upload File</h1>

        {{-- Menampilkan pesan error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form untuk upload file --}}
        <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Pilih File:</label>
                <input type="file" name="file" class="form-control" id="file">
                @if ($errors->has('file'))
                    <span class="text-danger">{{ $errors->first('file') }}</span>
                @endif
            </div>
            <button type="submit" class="btn btn-primary mt-3">Upload</button>
        </form>
    </div>
@endsection
