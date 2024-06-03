<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTimeline extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_timelines';
    protected $softDelete = true;

    protected $hidden = ['password', 'deleted_at'];

    public function product_items()
    {
        return $this->hasMany(ProductItem::class, 'product_timeline_id');
    }

    public function projects()
    {
        return $this->belongsTo(Project::class);
    }
}
