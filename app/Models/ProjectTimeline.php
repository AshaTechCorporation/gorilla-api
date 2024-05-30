<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTimeline extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_timelines';
    protected $softDelete = true;

    protected $hidden = ['password', 'deleted_at'];

    //////////////////////////////////////// relation //////////////////////////////////////
    public function influencers()
    {
        return $this->belongsToMany(Influencer::class);
    }

    public function product_items()
    {
        return $this->belongsTo(ProductItem::class);
    }
}
