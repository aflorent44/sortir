<?php

namespace App\Enum;

enum EventStatus: string
{
    case CREATED = 'créée';
    case OPENED = 'ouverte';
    case CLOSED = 'clôturée';
    case PENDING = 'en cours';
    case ENDED = 'terminée';
    case CANCELLED = 'annulée';
    case ARCHIVED = 'archivée';

}