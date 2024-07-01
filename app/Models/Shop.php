<?php

namespace App\Models;

use App\Models\User;
use App\Models\ShopCatalog;
use App\Models\ProductRating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function whatsapp(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!str_starts_with($value, "62"))
                    return "62" . $value;
            }
        );
    }

    public function averageRatings()
    {
        $totalRatings = 0;
        $productsWithReview = $this->products()->has('ratings')->with('ratings')->withSum('ratings', 'rating')->get();

        foreach ($productsWithReview as $product) {
            $totalRatings += $product->ratings_sum_rating;
        }

        if ($totalRatings == 0)
            return 0;

        $averageRatings = $totalRatings / $productsWithReview->count();

        $this->total_ratings = $averageRatings;

        return $averageRatings;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function catalog()
    {
        return $this->hasMany(ShopCatalog::class, 'shop_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function product_ratings()
    {
        return $this->hasMany(ProductRating::class, 'shop_id');
    }

    public function getID($user)
    {
        return Shop::where('user_id', $user)->firstOrFail('id');
    }

    public function getRouteKeyName()
    {
        return 'url';
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%")->orWhere('desc', 'LIKE', "%{$search}%");
        });
    }
}
