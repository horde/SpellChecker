<?php

declare(strict_types=1);

/**
 * Copyright 2005-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker;

enum SuggestMode: int
{
    case Fast = 1;
    case Normal = 2;
    case Slow = 3;
}
