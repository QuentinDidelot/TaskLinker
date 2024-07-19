<?php

namespace App\Enum;

enum StatutLibelle : string {
    case TO_DO = 'to_do';
    case DOING = 'doing';
    case DONE = 'done';

    public static function getValues(): array
    {
        return array_map(fn(self $statut): string => $statut->value, self::cases());
    }
};