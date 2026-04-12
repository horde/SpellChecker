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
use Horde\SpellChecker\Aspell;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_SpellChecker_Aspell::class)]
#[CoversClass(Aspell::class)]
class WrapperParityTest extends TestCase
{
    private string $path;

    public function setUp(): void
    {
        $path = trim((string) shell_exec('which aspell'));
        if (!is_executable($path)) {
            $path = trim((string) shell_exec('which ispell'));
        }

        if (!is_executable($path)) {
            $this->markTestSkipped('No aspell/ispell binary found.');
        }

        $this->path = $path;
    }

    public function testLibAndSrcProduceIdenticalResults(): void
    {
        $legacy = new Horde_SpellChecker_Aspell(['path' => $this->path]);
        $modern = new Aspell(path: $this->path);

        $text = 'some tet [mispeled] correctly ?';

        $legacyResult = $legacy->spellCheck($text);
        $modernResult = $modern->spellCheck($text)->toArray();

        $this->assertSame($legacyResult['bad'], $modernResult['bad']);
        $this->assertSame($legacyResult['suggestions'], $modernResult['suggestions']);
    }

    public function testSetParamsBetweenSpellCheckCalls(): void
    {
        $legacy = new Horde_SpellChecker_Aspell(['path' => $this->path]);

        $result1 = $legacy->spellCheck('tet mispeled');
        $this->assertContains('tet', $result1['bad']);

        $legacy->setParams(['localDict' => ['tet']]);

        $result2 = $legacy->spellCheck('tet mispeled');
        $this->assertNotContains('tet', $result2['bad']);
        $this->assertContains('mispeled', $result2['bad']);
    }

    public function testDynamicClassnameInstantiation(): void
    {
        $driver = 'aspell';
        $classname = 'Horde_SpellChecker_' . ucfirst($driver);

        $this->assertTrue(class_exists($classname));

        $instance = new $classname(['path' => $this->path]);
        $result = $instance->spellCheck('tet');

        $this->assertArrayHasKey('bad', $result);
        $this->assertArrayHasKey('suggestions', $result);
        $this->assertContains('tet', $result['bad']);
    }
}
