@extends('layouts.app')

@section('title', 'List of Uploaded Files')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">List of Uploaded Files</h1>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Original Name</th>
                <th>Encrypted Name</th>
                <th>File Path</th>
                <th>Uploaded At</th>
                <th>Public Key</th>
                <th>Private Key</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($uploads as $upload)
            <tr>
                <td>{{ $upload->id }}</td>
                <td>{{ $upload->original_name }}</td>
                <td>{{ $upload->encrypted_name }}</td>
                <td>{{ $upload->file_path }}</td>
                <td>{{ $upload->created_at }}</td>
                <td>{{ $upload-> public_key}}</td>
                <td>{{ $upload-> private_key}}</td>
                <td>
                    <a href="{{ route('file.decrypt', $upload->id) }}" class="btn btn-primary btn-sm">Download Decrypted</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection