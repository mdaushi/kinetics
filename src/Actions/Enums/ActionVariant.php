<?php

namespace Kinetics\Actions\Enums;

enum ActionVariant: string
{
    case DEFAULT = 'default';
    case DESTRUCTIVE = 'destructive';
    case GHOST = 'ghost';
    case OUTLINE = 'outline';
    case SECONDARY = 'secondary';
    case LINK = 'link';
}
