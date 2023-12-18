<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoryblog extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'category_blog';

    protected $guarded = ['id'];
}
