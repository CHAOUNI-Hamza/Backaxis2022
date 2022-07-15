<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class Sabonner extends Model
{
    use HasFactory, SoftDeletes, Notifiable;
    protected $table = 'sabonners';
    protected $fillable = [
        'id', 'email'
    ];
}
