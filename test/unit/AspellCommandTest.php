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
use Horde\SpellChecker\SuggestMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Aspell::class)]
class AspellCommandTest extends TestCase
{
    public function testDefaultCommand(): void
    {
        $aspell = new Aspell();
        $cmd = $aspell->buildCommand();

        $this->assertStringContainsString('aspell', $cmd);
        $this->assertStringContainsString('-a', $cmd);
        $this->assertStringContainsString('--encoding=UTF-8', $cmd);
        $this->assertStringContainsString('--sug-mode=fast', $cmd);
        $this->assertStringContainsString("--lang='en'", $cmd);
    }

    public function testCustomPath(): void
    {
        $aspell = new Aspell(path: '/usr/bin/aspell');
        $cmd = $aspell->buildCommand();

        $this->assertStringStartsWith('/usr/bin/aspell ', $cmd);
    }

    public function testSlowSuggestMode(): void
    {
        $aspell = new Aspell(suggestMode: SuggestMode::Slow);
        $cmd = $aspell->buildCommand();

        $this->assertStringContainsString('--sug-mode=bad-spellers', $cmd);
    }

    public function testNormalSuggestMode(): void
    {
        $aspell = new Aspell(suggestMode: SuggestMode::Normal);
        $cmd = $aspell->buildCommand();

        $this->assertStringContainsString('--sug-mode=normal', $cmd);
    }

    public function testLocale(): void
    {
        $aspell = new Aspell(locale: 'de');
        $cmd = $aspell->buildCommand();

        $this->assertStringContainsString("--lang='de'", $cmd);
    }

    public function testMinLength(): void
    {
        $aspell = new Aspell(minLength: 5);
        $cmd = $aspell->buildCommand();

        $this->assertStringContainsString("--ignore='4'", $cmd);
    }

    public function testMinLengthZero(): void
    {
        $aspell = new Aspell(minLength: 0);
        $cmd = $aspell->buildCommand();

        $this->assertStringContainsString("--ignore='0'", $cmd);
    }

    public function testHtmlMode(): void
    {
        $aspell = new Aspell(html: true);
        $cmd = $aspell->buildCommand();

        $this->assertStringContainsString('-H', $cmd);
        $this->assertStringContainsString('--rem-html-check=alt', $cmd);
    }

    public function testNonHtmlOmitsHtmlFlags(): void
    {
        $aspell = new Aspell(html: false);
        $cmd = $aspell->buildCommand();

        $this->assertStringNotContainsString('-H', $cmd);
        $this->assertStringNotContainsString('--rem-html-check', $cmd);
    }
}
