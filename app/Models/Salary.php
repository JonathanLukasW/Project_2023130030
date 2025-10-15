<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = ['salary', 'bonuses', 'deductions', 'net_salary'];
}
