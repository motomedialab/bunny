<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Enums;

enum SmartGenerateStatus: int
{
    case None = 0;
    case Queued = 1;
    case InProgress = 2;
    case Finished = 3;
    case Failed = 4;
}
