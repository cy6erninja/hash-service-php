<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hash extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'hashes';
    protected $fillable = ['data', 'data_hash'];
}
