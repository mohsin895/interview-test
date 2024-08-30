<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'category_id'];

    public function categories(){
        return $this->hasOne(Category::class,'id','category_id');
    }
}
