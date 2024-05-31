<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';
    protected $softDelete = true;

    protected $hidden = ['password', 'deleted_at'];

    //////////////////////////////////////// relation //////////////////////////////////////
    public function influencers()
    {
        return $this->belongsToMany(Influencer::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function projects()
    {
        return $this->belongsTo(Project::class);
    }

    public function product_items()
    {
        return $this->hasMany(ProductItem::class);
    }

}
