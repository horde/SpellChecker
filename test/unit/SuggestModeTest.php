<?php

declare(strict_types=1);

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker\Test\Unit;

use Horde\SpellChecker\SuggestMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SuggestMode::class)]
class SuggestModeTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertSame(1, SuggestMode::Fast->value);
        $this->assertSame(2, SuggestMode::Normal->value);
        $this->assertSame(3, SuggestMode::Slow->value);
    }

    public function testFromInt(): void
    {
        $this->assertSame(SuggestMode::Fast, SuggestMode::from(1));
        $this->assertSame(SuggestMode::Normal, SuggestMode::from(2));
        $this->assertSame(SuggestMode::Slow, SuggestMode::from(3));
    }

    public function testTryFromInvalid(): void
    {
        $this->assertNull(SuggestMode::tryFrom(0));
        $this->assertNull(SuggestMode::tryFrom(99));
    }

    public function testCasesCount(): void
    {
        $this->assertCount(3, SuggestMode::cases());
    }
}
