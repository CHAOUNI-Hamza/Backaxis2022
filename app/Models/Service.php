<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes, Notifiable;
    protected $table = 'services';
    protected $fillable = [
        'id', 'title', 'photo', 'slug'
    ];

    /**
     * Get the comments for the blog post.
     */
    public function produit()
    {
        return $this->hasMany(Produit::class);
    }

    protected static function boot() {
        parent::boot();
    
        static::saving(function ($service) {
            $service->slug = Str::slug($service->title, '-');
        });
        }
    
        public function getRouteKeyName() {
            return 'slug';
        }
}
