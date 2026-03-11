<?php

namespace App\Enums;

enum NewsBlockType: string
{
    case Text = 'text';
    case Image = 'image';
    case TextImageRight = 'text_image_right';
    case TextImageLeft = 'text_image_left';

    public function hasText(): bool{
        return in_array($this, [self::Text, self::TextImageLeft, self::TextImageRight]);
    }

    public function hasImage(): bool{
        return in_array($this, [self::Image, self::TextImageLeft, self::TextImageRight]);
    }

    public static function values(): array{
        return [
            self::Text->value,
            self::Image->value,
            self::TextImageLeft->value,
            self::TextImageRight->value,
        ];
    }   
}
