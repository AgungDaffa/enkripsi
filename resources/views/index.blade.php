@extends('layouts.app')

@section('title', 'Upload File')

@section('content')
<form action="/upload" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file">
    <button type="submit">Upload</button>
</form>
@endsection
