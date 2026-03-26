<?php

namespace App\Support\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case LECTURER = 'lecturer';
    case STUDENT = 'student';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::LECTURER => 'Lecturer',
            self::STUDENT => 'Student',
        };
    }
}