<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'organizer_id',
    'title',
    'slug',
    'description',
    'venue',
    'city',
    'event_category_id',
    'starts_at',
    'ends_at',
    'cover_path',
    'status',
])]
class Event extends Model
{
    protected function casts(): array
    {
        return [
            'status' => EventStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (Event $event): void {
            if (blank($event->slug)) {
                $event->slug = static::uniqueSlug($event->title);
            }
        });
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'etkinlik';
        $slug = $base;
        $i = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function categoryLabel(): string
    {
        return $this->eventCategory?->name ?? 'Etkinlik';
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::Published);
    }

    public function scopeManageableBy(Builder $query, User $user): Builder
    {
        $query->orderBy('starts_at');

        return $user->isAdmin()
            ? $query
            : $query->where('organizer_id', $user->id);
    }

    public function soldCount(): int
    {
        return $this->tickets()->count();
    }

    public function capacity(): int
    {
        return (int) $this->ticketTypes()->sum('capacity');
    }

    public function remainingCount(): int
    {
        return max(0, $this->capacity() - $this->soldCount());
    }

    public function checkedInCount(): int
    {
        return $this->tickets()->where('status', 'used')->count();
    }

    public function startingPrice(): ?int
    {
        $price = $this->ticketTypes()->min('price');

        return $price === null ? null : (int) $price;
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? asset('storage/'.$this->cover_path) : null;
    }

    public function displayTitle(): string
    {
        return mb_strtoupper($this->title, 'UTF-8');
    }
}
