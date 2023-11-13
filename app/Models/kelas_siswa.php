<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kelas_siswa extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'kelas_siswa';

    protected $guarded = ['id'];
}
