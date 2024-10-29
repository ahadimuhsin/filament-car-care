<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarService extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ["name", "slug", "price", "about", "photo", "duration_in_hour", "icon"];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        $slug = Str::slug($value);

        $this->attributes['slug'] = $slug;
    }

    public function storeServices(): HasMany
    {
        return $this->hasMany(StoreService::class, "car_store_id");
    }
}
