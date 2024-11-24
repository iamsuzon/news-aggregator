<?php

namespace App\Enums;

enum PostVisibilityEnum: int
{
    case Private = 0;
    case Public = 1;
    case Draft = 2;
}
