<?php

namespace App\Domain\FileMovement\Enums;

enum enFileMovement: string
{
    case InsideArchive = 'InsideArchive';
    case OutsideArchive = 'OutsideArchive';
    case Missing = 'Missing';
}
