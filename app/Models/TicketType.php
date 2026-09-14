<?php

namespace App\Models;

use App\Enums\TicketSaleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['event_id', 'name', 'description', 'price', 'capacity', 'sale_status'])]
class TicketType extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'capacity' => 'integer',
            'sale_status' => TicketSaleStatus::class,
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function soldCount(): int
    {
        return $this->tickets()->count();
    }

    public function remainingCount(): int
    {
        return max(0, $this->capacity - $this->soldCount());
    }

    public function isOnSale(): bool
    {
        return $this->sale_status === TicketSaleStatus::OnSale && $this->remainingCount() > 0;
    }

    public function canBeDeleted(): bool
    {
        return $this->soldCount() === 0;
    }
}
