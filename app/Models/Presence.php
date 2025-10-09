<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['check_in', 'check_out', 'date', 'status'];
}
