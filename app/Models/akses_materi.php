<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class akses_materi extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'akses_materi';

    protected $guarded = ['id'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
