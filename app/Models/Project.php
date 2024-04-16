<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'projects';
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
