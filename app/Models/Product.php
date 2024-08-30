<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
   
    public function categoryProducts(){
        return $this->hasMany(CategoryProduct::class);
    }
    public function feature(){
        return $this->hasMany(Feature::class);
    }
}
