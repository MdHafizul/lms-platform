<?php

namespace App\Support\Enums;

enum ActivityType: string
{
    case CONTENT = 'CONTENT';
    case ACTIVITY = 'ACTIVITY';
    case ASSESSMENT = 'ASSESSMENT';

    public function label(): string
    {
        return match ($this) {
            self::CONTENT => 'Content',
            self::ACTIVITY => 'Activity',
            self::ASSESSMENT => 'Assessment',
        };
    }

    public static function all(): array
    {
        return [
            self::CONTENT->value => self::CONTENT->label(),
            self::ACTIVITY->value => self::ACTIVITY->label(),
            self::ASSESSMENT->value => self::ASSESSMENT->label(),
        ];
    }
}
