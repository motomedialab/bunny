<?php

namespace Motomedialab\Bunny\Enums;

enum TranscodingError: int
{
    case Undefined = 0;
    case StreamLengthsDifference = 1;
    case TranscodingWarnings = 2;
    case IncompatibleResolution = 3;
    case InvalidFramerate = 4;
    case VideoExceededMaxDuration = 5;
    case AudioExceededMaxDuration = 6;
    case OriginalCorrupted = 7;
    case TranscriptionFailed = 8;
    case JitIncompatible = 9;
    case JitFailed = 10;
}