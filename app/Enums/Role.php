<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Organizer = 'organizer';
    case Attendee = 'attendee';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Yönetici',
            self::Organizer => 'Organizatör',
            self::Attendee => 'Katılımcı',
        };
    }
}