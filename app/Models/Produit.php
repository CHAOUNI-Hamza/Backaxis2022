<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Produit extends Model
{
    use HasFactory, SoftDeletes, Notifiable;
    protected $table = 'produits';
    protected $fillable = [
        'id', 'title', 'photo', 'description', 'social', 'slug', 'service_id'
    ];
    /**
     * Get the comments for the blog post.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    protected static function boot() {
        parent::boot();
    
        static::saving(function ($produit) {
            $produit->slug = Str::slug($produit->title, '-');
        });
        }
    
        public function getRouteKeyName() {
            return 'slug';
        }
}
