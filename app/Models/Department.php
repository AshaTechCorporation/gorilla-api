<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'departments';
    protected $softDelete = true;

    protected $hidden = ['password', 'deleted_at'];

        //////////////////////////////////////// relation //////////////////////////////////////

        public function employees()
        {
            return $this->hasMany(Employee::class);
        }
}
