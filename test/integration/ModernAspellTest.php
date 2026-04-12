<?php

declare(strict_types=1);

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker\Test\Integration;

use Horde\SpellChecker\Aspell;
use Horde\SpellChecker\SpellCheckResult;
use Horde\SpellChecker\SuggestMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

#[CoversClass(Aspell::class)]
class ModernAspellTest extends TestCase
{
    private Aspell $aspell;

    public function setUp(): void
    {
        $path = trim((string) shell_exec('which aspell'));
        if (!is_executable($path)) {
            $path = trim((string) shell_exec('which ispell'));
        }

        if (!is_executable($path)) {
            $this->markTestSkipped('No aspell/ispell binary found.');
        }

        $this->aspell = new Aspell(path: $path);
    }

    public function testSpellCheckReturnType(): void
    {
        $result = $this->aspell->spellCheck('some tet [mispeled] ?');
        $this->assertInstanceOf(SpellCheckResult::class, $result);
    }

    public function testSpellCheckDetectsMisspelledWords(): void
    {
        $result = $this->aspell->spellCheck('some tet [mispeled] ?');

        $this->assertSame(['tet', 'mispeled'], $result->bad);
        $this->assertCount(2, $result->suggestions);
        $this->assertNotEmpty($result->suggestions[0]);
        $this->assertNotEmpty($result->suggestions[1]);
    }

    public function testSpellCheckCorrectTextReturnsEmpty(): void
    {
        $result = $this->aspell->spellCheck('hello world');

        $this->assertSame([], $result->bad);
        $this->assertSame([], $result->suggestions);
    }

    public function testToArrayMatchesLegacyFormat(): void
    {
        $result = $this->aspell->spellCheck('some tet ?');
        $array = $result->toArray();

        $this->assertArrayHasKey('bad', $array);
        $this->assertArrayHasKey('suggestions', $array);
        $this->assertContains('tet', $array['bad']);
    }

    public function testLocalDictExcludesWords(): void
    {
        $aspell = new Aspell(
            path: $this->aspell->buildCommand() ? $this->getPath() : 'aspell',
            localDict: ['tet'],
        );
        $result = $aspell->spellCheck('some tet mispeled');

        $this->assertNotContains('tet', $result->bad);
        $this->assertContains('mispeled', $result->bad);
    }

    public function testHtmlMode(): void
    {
        $aspell = new Aspell(
            path: $this->getPath(),
            html: true,
        );
        $result = $aspell->spellCheck('<p>some tet</p>');

        $this->assertContains('tet', $result->bad);
        $this->assertNotContains('p', $result->bad);
    }

    public function testMaxSuggestionsLimitsOutput(): void
    {
        $aspell = new Aspell(
            path: $this->getPath(),
            maxSuggestions: 2,
        );
        $result = $aspell->spellCheck('mispeled');

        $this->assertNotEmpty($result->bad);
        foreach ($result->suggestions as $suggestions) {
            $this->assertLessThanOrEqual(2, count($suggestions));
        }
    }

    public function testDuplicateMisspelledWordAppearsOnce(): void
    {
        $result = $this->aspell->spellCheck('tet tet tet');

        $this->assertSame(['tet'], $result->bad);
        $this->assertCount(1, $result->suggestions);
    }

    private function getPath(): string
    {
        $ref = new ReflectionProperty($this->aspell, 'path');
        $ref->setAccessible(true);

        return $ref->getValue($this->aspell);
    }
}
