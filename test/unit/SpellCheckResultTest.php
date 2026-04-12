<?php

declare(strict_types=1);

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker\Test\Unit;

use Horde\SpellChecker\SpellCheckResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SpellCheckResult::class)]
class SpellCheckResultTest extends TestCase
{
    public function testEmptyResult(): void
    {
        $result = new SpellCheckResult();
        $this->assertSame([], $result->bad);
        $this->assertSame([], $result->suggestions);
    }

    public function testWithData(): void
    {
        $result = new SpellCheckResult(
            bad: ['tet', 'mispeled'],
            suggestions: [['test', 'tat'], ['misspelled', 'misplaced']],
        );

        $this->assertSame(['tet', 'mispeled'], $result->bad);
        $this->assertCount(2, $result->suggestions);
        $this->assertSame(['test', 'tat'], $result->suggestions[0]);
    }

    public function testToArray(): void
    {
        $result = new SpellCheckResult(
            bad: ['foo'],
            suggestions: [['bar', 'baz']],
        );

        $this->assertSame(
            ['bad' => ['foo'], 'suggestions' => [['bar', 'baz']]],
            $result->toArray(),
        );
    }

    public function testToArrayEmpty(): void
    {
        $this->assertSame(
            ['bad' => [], 'suggestions' => []],
            (new SpellCheckResult())->toArray(),
        );
    }
}
