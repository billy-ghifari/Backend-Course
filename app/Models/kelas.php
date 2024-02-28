<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class kelas extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'kelas';

    protected $guarded = ['id'];

    protected $fillable = ['nama', 'deskripsi', 'foto_thumbnail', 'r_id_non_siswa', 'r_id_category', 'trash'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
