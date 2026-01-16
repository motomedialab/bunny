<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Enums;

enum VideoResolution: string
{
    case Res240 = '240p';
    case Res360 = '360p';
    case Res480 = '480p';
    case Res720 = '720p';
    case Res1080 = '1080p';
    case Res1440 = '1440p';
    case Res2160 = '2160p';

    public function label(): string
    {
        return match ($this) {
            VideoResolution::Res240 => 'EGA 352 x 240',
            VideoResolution::Res360 => 'nHD 640 x 360',
            VideoResolution::Res480 => 'ED 842 x 480',
            VideoResolution::Res720 => 'HD 1280 x 720',
            VideoResolution::Res1080 => 'FHD 1920 x 1080',
            VideoResolution::Res1440 => 'QHD 2560 x 1440',
            VideoResolution::Res2160 => 'UHD 3840 x 2160',
        };
    }

    public function kbps(): int
    {
        return match ($this) {
            VideoResolution::Res240 => 600,
            VideoResolution::Res360 => 800,
            VideoResolution::Res480 => 1400,
            VideoResolution::Res720 => 2800,
            VideoResolution::Res1080 => 5000,
            VideoResolution::Res1440 => 8000,
            VideoResolution::Res2160 => 25000,
        };
    }
}
