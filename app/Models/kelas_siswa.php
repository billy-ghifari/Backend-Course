<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class kelas_siswa extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'kelas_siswa';

    protected $guarded = ['id'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
