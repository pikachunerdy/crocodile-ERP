<?php

namespace App\Model\Commercial;

use App\Model\Accounting\SuppliersPayment;
use App\Model\Inventory\PurchaseReturn;
use App\Model\User\User;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    protected $guarded=[];

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseReturn()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function supplierPayment()
    {
        return $this->hasMany(SuppliersPayment::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
