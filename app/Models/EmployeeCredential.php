<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeCredential extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employee_credentials';
    protected $softDelete = true;

    protected $hidden = ['deleted_at'];

    //////////////////////////////////////// relation //////////////////////////////////////
    public function employees()
    {
        return $this->belongsTo(Employee::class,'employee_id', 'id');
    }

}
