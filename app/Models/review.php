<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'review';

    protected $guarded = ['id'];
}
