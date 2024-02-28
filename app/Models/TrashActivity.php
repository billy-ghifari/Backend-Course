<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrashActivity extends Model
{
    use HasFactory;

    protected $primarykey = 'id';

    protected $table = 'trash_activities';

    protected $guarded = ['id'];

    protected $fillable = ['model_id', 'deleted_by'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
