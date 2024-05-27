<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfluencerProjectTimeline extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'influencer_project_timelines';
    protected $softDelete = true;

    protected $hidden = ['deleted_at'];
}
