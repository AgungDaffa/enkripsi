<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RSA;
use App\Models\Upload;

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
        $fileExtension = $file->getClientOriginalExtension(); // Ambil ekstensi file

        // Pisahkan nama file dari ekstensi
        $fileNameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);

        // Generate bilangan prima untuk RSA
        $p = 61;
        $q = 53;

        // Inisialisasi RSA dengan bilangan prima
        $rsa = new RSA($p, $q);

        // Enkripsi nama file menggunakan RSA tanpa ekstensi
        $encryptedName = $rsa->encrypt($fileNameWithoutExtension);

        // Encode nama file terenkripsi
        $encryptedFileName = base64_encode($encryptedName);

        // Tambahkan ekstensi file kembali ke nama file
        $encryptedFileNameWithExtension = $encryptedFileName . '.' . $fileExtension;

        // Simpan file ke direktori tertentu
        $path = $file->storeAs('public/uploads', $encryptedFileNameWithExtension);

        // Simpan informasi file ke database
        $upload = new Upload();
        $upload->original_name = $originalName;
        $upload->encrypted_name = $encryptedFileName; // Simpan nama file terenkripsi tanpa ekstensi
        $upload->file_path = $path;
        $upload->public_key = $rsa->getPublicKey();
        $upload->private_key = $rsa->getPrivateKey();
        $upload->save();

        // Redirect ke halaman list uploads
        return redirect()->route('uploads.list')->with('success', 'File uploaded successfully!');
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

        // Ambil nama file terenkripsi dari database
        $encryptedName = $upload->encrypted_name;

        // Dekripsi nama file menggunakan RSA
        $decryptedName = $rsa->decrypt(base64_decode($encryptedName));

        // Gabungkan nama file yang didekripsi dengan ekstensi asli
        $originalName = $upload->original_name;

        // Buat path file dari file_path di database
        $filePath = storage_path('app/' . $upload->file_path);

        // Periksa apakah file ada
        if (!file_exists($filePath)) {
            return abort(404, 'File not found.');
        }

        // Mengunduh file atau menampilkan file sesuai dengan kebutuhan Anda
        return response()->download($filePath, $originalName);
    }
}
