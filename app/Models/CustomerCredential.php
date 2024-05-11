<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCredential extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customer_credentials';
    protected $softDelete = true;

    protected $hidden = ['deleted_at'];

    //////////////////////////////////////// relation //////////////////////////////////////
    public function customers()
    {
        return $this->belongsTo(Customer::class);
    }
}
