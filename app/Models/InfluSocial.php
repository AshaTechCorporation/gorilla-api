<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfluSocial extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'influ_socials';
    protected $softDelete = true;

    protected $hidden = ['deleted_at'];
}

