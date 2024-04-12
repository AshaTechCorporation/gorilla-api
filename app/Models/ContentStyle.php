<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentStyle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'content_style';
    protected $softDelete = true;

    protected $hidden = ['password', 'deleted_at'];

    //////////////////////////////////////// relation //////////////////////////////////////
    public function Influencers()
    {
        return $this->hasMany(Influencer::class);
    }
}
