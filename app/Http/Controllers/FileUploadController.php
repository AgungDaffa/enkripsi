<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RSA;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;


class FileUploadController extends Controller
{
    public function listUploads()
    {
        $uploads = Upload::all();

        return view('list', compact('uploads'));
    }

public function upload(Request $request)
{
    // Validasi inputan file
    $request->validate([
        'file' => 'required|file|max:10240',
    ]);

    // Ambil file dari request
    $file = $request->file('file');
    $originalName = $file->getClientOriginalName();
    $fileExtension = $file->getClientOriginalExtension();

    // Baca isi file
    $fileContent = file_get_contents($file->getRealPath());

    // Generate bilangan prima untuk RSA
    $p = 61;
    $q = 53;

    // Inisialisasi RSA dengan bilangan prima
    $rsa = new RSA($p, $q);

    // Enkripsi isi file menggunakan RSA
    $encryptedContent = $rsa->encrypt($fileContent);

    // Simpan file terenkripsi ke direktori
    $encryptedFileName = base64_encode($rsa->encrypt(pathinfo($originalName, PATHINFO_FILENAME))) . '.' . $fileExtension;
    $path = 'public/uploads/' . $encryptedFileName;
    Storage::put($path, $encryptedContent);

    // Simpan informasi file ke database
    $upload = new Upload();
    $upload->original_name = $originalName;
    $upload->encrypted_name = $encryptedFileName; // Simpan nama file terenkripsi di database
    $upload->file_path = $path;
    $upload->public_key = $rsa->getPublicKey();
    $upload->private_key = $rsa->getPrivateKey();
    $upload->save();

    // Redirect ke halaman list uploads
    return redirect()->route('uploads.list')->with('success', 'File uploaded and encrypted successfully!');
}

public function decryptFile($id)
{
    // Ambil informasi file dari database berdasarkan ID
    $upload = Upload::findOrFail($id);

    // Generate bilangan prima untuk RSA
    $p = 61;
    $q = 53;

    // Inisialisasi RSA dengan bilangan prima
    $rsa = new RSA($p, $q);

    // Ambil path file terenkripsi dari database
    $encryptedFilePath = storage_path('app/' . $upload->file_path);

    // Baca isi file terenkripsi
    $encryptedContent = file_get_contents($encryptedFilePath);

    // Dekripsi isi file menggunakan RSA
    $decryptedContent = $rsa->decrypt($encryptedContent);

    // Mengunduh file dengan nama asli
    return response()->streamDownload(function () use ($decryptedContent) {
        echo $decryptedContent;
    }, $upload->original_name);
}
}
