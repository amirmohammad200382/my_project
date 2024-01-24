<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'inventory',
        'description',
        'user_id'
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('count');
    }
    public function users() {
        return $this->belongsTo( User::class);
    }
}
