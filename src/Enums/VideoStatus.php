<?php

declare(strict_types=1);

namespace Motomedialab\Bunny\Enums;

enum VideoStatus: int
{
    case Created = 0;
    case Uploaded = 1;
    case Processing = 2;
    case Transcoding = 3;
    case Finished = 4;
    case Error = 5;
    case UploadFailed = 6;
    case JitSegmenting = 7;
    case JitPlaylistCreated = 8;
}
