<?php

namespace App\Enum;

enum EventStatus: string
{
    case CREATED = 'créée';
    case OPENED = 'ouverte';
    case CANCELLED = 'annulée';
    case CLOSED = 'terminée';
    case PENDING = 'en cours';

    public function toString(): string
    {
        return $this->value;
    }
}