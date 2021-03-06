<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    /**
     * Get the comments for the article.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
