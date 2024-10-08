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
    // Validasi inputan file dan nama file yang tidak lebih dari 10 karakter
    $request->validate([
        'file' => 'required|file|max:10240',
    ]);

    // Ambil file dari request
    $file = $request->file('file');
    $originalName = $file->getClientOriginalName();
    $fileNameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);

    // Validasi panjang nama file (tanpa ekstensi)
    if (strlen($fileNameWithoutExtension) > 10) {
        return redirect()->back()->withErrors(['file' => 'Nama file tidak boleh lebih dari 10 karakter.']);
    }

    $fileExtension = $file->getClientOriginalExtension();

    // Baca isi file
    $fileContent = file_get_contents($file->getRealPath());

    // Inisialisasi RSA dengan panjang bit yang diinginkan
    $rsa = new RSA(16); // Panjang bit bisa disesuaikan

    // Enkripsi isi file menggunakan RSA
    $encryptedContent = $rsa->encrypt($fileContent);

    // Simpan file terenkripsi ke direktori
    $encryptedFileName = base64_encode($rsa->encrypt($fileNameWithoutExtension)) . '.' . $fileExtension;
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
    return redirect()->route('uploads.encryption')->with('success', 'File uploaded and encrypted successfully!');
}


    public function decryptFile($id)
    {
        // Ambil informasi file dari database berdasarkan ID
        $upload = Upload::findOrFail($id);

        // Inisialisasi RSA dengan panjang bit yang diinginkan
        $rsa = new RSA(16); // Panjang bit harus sama dengan yang digunakan saat enkripsi

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

    public function downloadEncryptedFile($id)
    {
        // Ambil informasi file dari database berdasarkan ID
        $upload = Upload::findOrFail($id);

        // Ambil path file terenkripsi dari database
        $encryptedFilePath = storage_path('app/' . $upload->file_path);

        // Periksa apakah file ada
        if (!file_exists($encryptedFilePath)) {
            return redirect()->route('uploads.list')->with('error', 'Encrypted file not found.');
        }

        // Unduh file terenkripsi
        return response()->download($encryptedFilePath, $upload->encrypted_name);
    }

    // Menampilkan daftar file terenkripsi
    public function showEncryptionList()
    {
        $uploads = Upload::all(); // Sesuaikan query jika hanya ingin mengambil file terenkripsi
        return view('encryption_list', compact('uploads'));
    }

    // Menampilkan daftar file yang sudah didekripsi
    public function showDecryptionList()
    {
        $uploads = Upload::all(); // Sesuaikan query jika hanya ingin mengambil file yang sudah didekripsi
        return view('decryption_list', compact('uploads'));
    }
}
