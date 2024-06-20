<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}