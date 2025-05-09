<?php

declare(strict_types=1);

namespace App\Enums;

enum CivilStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Widowed = 'widowed';
    case Divorced = 'divorced';
    case Separated = 'separated';
    case FreeUnion = 'free_union';
    case Other = 'other';

}
