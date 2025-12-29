<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavbarItem extends Model
{
    protected $fillable = [
        'title', 'title_hi', 'slug', 'parent_id', 'is_dropdown',
        'order', 'is_active', 'route', 'url', 'icon', 'target'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavbarItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavbarItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->with('children'); // ✅ recursive load
    }

    public function getLinkAttribute(): string
    {
        if ($this->route && \Route::has($this->route)) {
            return route($this->route, ['locale' => app()->getLocale()]);
        }

        if ($this->slug) {
            return url(app()->getLocale() . '/' . $this->slug);
        }

        return $this->url ?? '#';
    }
}
