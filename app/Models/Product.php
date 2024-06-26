<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\ShopCatalog;
use Illuminate\Support\Str;
use App\Models\ProductRating;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    public $with = ['shop', 'subcategory'];

    public function getVisibility($num = null)
    {
        switch ($num ?? $this->visibility):
            case (1):
                return 'Public';

            case (2):
                return 'Unlisted';

            default:
                return 'Private';
        endswitch;
    }

    public function getAvgRatings()
    {
        if ($this->ratings->sum('rating') === 0)
            return 0;

        return $this->ratings->sum('rating') / $this->ratings->count();
    }

    public function getFirstImage(): string
    {
        $image = json_decode($this->image);
        if (!$image)
            return '';

        return $image[0];
    }

    // Relationships method
    public function catalog()
    {
        return $this->belongsTo(ShopCatalog::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }

    public function scopeVisibility($query, $visibility)
    {
        switch (Str::lower($visibility)) {
            case 'public':
                $visibility = 1;
                break;
            case 'private':
                $visibility = 0;
                break;
            case 'unlisted':
                $visibility = 2;
                break;
        }
        return $query->where([['visibility', $visibility]]);
    }

    public function scopeFilters($query, $filters = [])
    {
        $query->when($filters['orderBy'] ?? false, function ($query, $order) {
            if ($order != 'best_selling')
                return $query->orderBy('created_at', $order == 'latest' ? 'desc' : 'asc');
            else
                return $query->orderBy('sold', 'desc');
        });

        $query->when($filters['ratings'] ?? false, function ($query, $ratings) {
            return $query;
        });

        $query->when($filters['location'] ?? false, function ($query, $location) {
            $query->whereHas('shop', function ($query) use ($location) {
                $query->where(function ($query) use ($location) {
                    foreach ($location as $loc) {
                        $query->orWhere('location', 'LIKE', "%$loc%");
                    }
                });
            });
        });

        $query->when($filters['shop'] ?? false, function ($query, $shop) {
            $query->whereHas('shop', function ($query) use ($shop) {
                $query->where('url', $shop)->orWhere('name', 'LIKE', "%{$shop}%");
            });
        });

        $query->when($filters['category'] ?? $filters['subcategory'] ?? false, function ($query, $subcategory) {
            $query->whereHas('subcategory', function ($query) use ($subcategory) {
                $query->where('slug', $subcategory);
            });
        });

        $query->when($filters['condition'] ?? false, function ($query, $condition) {
            if ($condition != 'all')
                $query->where('condition', $condition);
        });

        $query->when($filters['min_price'] ?? false, function ($query, $min_price) {
            $query->where('price', '>=', $min_price);
        })->when($filters['max_price'] ?? false, function ($query, $max_price) {
            $query->where('price', '<=', $max_price);
        });
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOldest($query)
    {

        return $query->orderBy('created_at', 'asc');
    }

    public function scopeBest_selling($query)
    {
        return $query->orderBy('sold', 'desc');
    }

    public function scopeQuery($query, $search)
    {
        $query->when($search ?? false, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%")->orWhere('desc', 'LIKE', "%{$search}%");
        });
    }

    public function scopeCategory($query, $category)
    {
        $query->whereHas('subcategory', function ($query) use ($category) {
            $query->where('slug', $category);
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
