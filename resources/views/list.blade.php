<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Uploaded Files</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
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
                    <!-- <th>Public Key</th>
                    <th>Private Key</th> -->
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
                    <!-- <td>{{ $upload-> public_key}}</td>
                    <td>{{ $upload-> private_key}}</td> -->
                    <td>
                        <a href="{{ route('file.decrypt', $upload->id) }}" class="btn btn-primary btn-sm">Download</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>