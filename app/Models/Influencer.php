<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Influencer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'influencers';
    protected $softDelete = true;

    protected $hidden = ['deleted_at'];

    //////////////////////////////////////// relation //////////////////////////////////////
    public function platform_socials()
    {
        return $this->belongsToMany(PlatformSocial::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function career()
    {
        return $this->belongsTo(Career::class);
    }

    public function contentstyle()
    {
        return $this->belongsTo(ContentStyle::class, 'content_style_id');
    }
    
    public function past_project()
    {
        return $this->hasMany(PastProject::class);
    }
}
