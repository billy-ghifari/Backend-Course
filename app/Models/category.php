<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'category';

    protected $guarded = ['id'];
}
