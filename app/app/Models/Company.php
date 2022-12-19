<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function activity(){
        return $this->belongsTo(Activity::class, 'activityId', 'id');
    }

    public function address(){
        return $this->hasOne(Address::class, 'companyId', 'id');
    }

    public function user(){
        return $this->hasOne(Users::class, 'companyId', 'id');
    }
}
