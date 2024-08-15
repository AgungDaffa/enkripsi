<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uploads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_name',
        'encrypted_name',
        'file_path',
        'public_key',
        'private_key',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'original_name' => 'string',
        'encrypted_name' => 'string',
        'file_path' => 'string',
        'public_key' => 'string',
        'private_key' => 'string',
    ];
}
