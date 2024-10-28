<?php

namespace App\Models;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rental extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function car()
    {
        return $this->belongsTo(Car::class,'car_id');
    }//end method

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }//end method
}
