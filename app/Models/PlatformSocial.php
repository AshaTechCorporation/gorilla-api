<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformSocial extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'platform_socials';
    protected $softDelete = true;

    protected $hidden = ['deleted_at'];

    //////////////////////////////////////// relation //////////////////////////////////////

    public function influencers()
    {
        return $this->belongsToMany(Influencer::class);
    }
}
