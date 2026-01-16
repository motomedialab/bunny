<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Enums;

enum TranscodingMessageLevel: int
{
    case Undefined = 0;
    case Information = 1;
    case Warning = 2;
    case Error = 3;
}
