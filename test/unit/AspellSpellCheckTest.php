<?php

declare(strict_types=1);

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker\Test\Unit;

use Horde\SpellChecker\Aspell;
use Horde\SpellChecker\Exception\SpellCheckException;
use Horde\SpellChecker\SpellCheckResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Aspell::class)]
class AspellSpellCheckTest extends TestCase
{
    public function testEmptyTextReturnsEmptyResult(): void
    {
        $aspell = new Aspell();
        $result = $aspell->spellCheck('');

        $this->assertSame([], $result->bad);
        $this->assertSame([], $result->suggestions);
    }

    public function testWhitespaceOnlyReturnsEmptyResult(): void
    {
        $aspell = new Aspell();
        $result = $aspell->spellCheck('   ');

        $this->assertSame([], $result->bad);
        $this->assertSame([], $result->suggestions);
    }

    public function testConstructorPathDefault(): void
    {
        $aspell = new Aspell();
        $cmd = $aspell->buildCommand();

        $this->assertStringStartsWith('aspell ', $cmd);
    }

    public function testConstructorPathCustom(): void
    {
        $aspell = new Aspell(path: '/opt/aspell');
        $cmd = $aspell->buildCommand();

        $this->assertStringStartsWith('/opt/aspell ', $cmd);
    }

    public function testBadBinaryThrowsSpellCheckException(): void
    {
        $aspell = new Aspell(path: '/nonexistent/aspell');

        $this->expectException(SpellCheckException::class);
        $this->expectExceptionMessageMatches('/Spellcheck failed/');
        $aspell->spellCheck('hello');
    }
}
