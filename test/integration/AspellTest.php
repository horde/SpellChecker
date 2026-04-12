<?php

declare(strict_types=1);

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker\Test\Integration;

use Horde_SpellChecker_Aspell;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_SpellChecker_Aspell::class)]
class AspellTest extends TestCase
{
    protected Horde_SpellChecker_Aspell $aspell;

    public function setUp(): void
    {
        $aspell = trim((string) shell_exec('which aspell'));
        if (!is_executable($aspell)) {
            $aspell = trim((string) shell_exec('which ispell'));
        }

        if (!is_executable($aspell)) {
            $this->markTestSkipped('No aspell/ispell binary found.');
        }

        $this->aspell = new Horde_SpellChecker_Aspell([
            'path' => $aspell,
        ]);
    }

    public function testSpellCheckDetectsMisspelledWords(): void
    {
        $res = $this->aspell->spellCheck('some tet [mispeled] ?');

        $this->assertNotEmpty($res);
        $this->assertNotEmpty($res['bad']);
        $this->assertEquals(
            ['tet', 'mispeled'],
            $res['bad']
        );
        $this->assertNotEmpty($res['suggestions']);
        $this->assertNotEmpty($res['suggestions'][0]);
        $this->assertNotEmpty($res['suggestions'][1]);
    }
}
