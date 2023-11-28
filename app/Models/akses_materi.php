<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class akses_materi extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'akses_materi';

    protected $guarded = ['id'];
}
