<?php

declare(strict_types=1);

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker\Test\Unit;

use Horde_SpellChecker;
use Horde_SpellChecker_Aspell;
use Horde_SpellChecker_Exception;
use Horde\SpellChecker\SuggestMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

#[CoversClass(Horde_SpellChecker_Aspell::class)]
#[CoversClass(Horde_SpellChecker::class)]
class LegacyAspellWrapperTest extends TestCase
{
    public function testConstructorDefaultPath(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell();
        $params = $this->getParams($wrapper);

        $this->assertSame('aspell', $params['path']);
    }

    public function testConstructorCustomPath(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell(['path' => '/usr/bin/aspell']);
        $params = $this->getParams($wrapper);

        $this->assertSame('/usr/bin/aspell', $params['path']);
    }

    public function testConstructorMergesParams(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell([
            'locale' => 'de',
            'html' => true,
            'maxSuggestions' => 5,
        ]);
        $params = $this->getParams($wrapper);

        $this->assertSame('de', $params['locale']);
        $this->assertTrue($params['html']);
        $this->assertSame(5, $params['maxSuggestions']);
        $this->assertSame('aspell', $params['path']);
    }

    public function testSetParamsInvalidatesDelegate(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell(['path' => '/first']);

        $ref = new ReflectionProperty($wrapper, '_delegate');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($wrapper));

        $wrapper->setParams(['path' => '/second']);
        $this->assertNull($ref->getValue($wrapper));

        $params = $this->getParams($wrapper);
        $this->assertSame('/second', $params['path']);
    }

    public function testResolveSuggestModeFromInt(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell(['suggestMode' => 2]);
        $ref = new ReflectionMethod($wrapper, 'resolveSuggestMode');
        $ref->setAccessible(true);

        $this->assertSame(SuggestMode::Normal, $ref->invoke($wrapper));
    }

    public function testResolveSuggestModeFromEnum(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell(['suggestMode' => SuggestMode::Slow]);
        $ref = new ReflectionMethod($wrapper, 'resolveSuggestMode');
        $ref->setAccessible(true);

        $this->assertSame(SuggestMode::Slow, $ref->invoke($wrapper));
    }

    public function testResolveSuggestModeDefault(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell();
        $ref = new ReflectionMethod($wrapper, 'resolveSuggestMode');
        $ref->setAccessible(true);

        $this->assertSame(SuggestMode::Fast, $ref->invoke($wrapper));
    }

    public function testConstants(): void
    {
        $this->assertSame(SuggestMode::Fast->value, Horde_SpellChecker::SUGGEST_FAST);
        $this->assertSame(SuggestMode::Normal->value, Horde_SpellChecker::SUGGEST_NORMAL);
        $this->assertSame(SuggestMode::Slow->value, Horde_SpellChecker::SUGGEST_SLOW);
    }

    public function testExceptionTranslation(): void
    {
        $wrapper = new Horde_SpellChecker_Aspell(['path' => '/nonexistent/aspell']);

        $this->expectException(Horde_SpellChecker_Exception::class);
        $this->expectExceptionMessageMatches('/Spellcheck failed/');
        $wrapper->spellCheck('hello');
    }

    /** @return array<string, mixed> */
    private function getParams(Horde_SpellChecker $wrapper): array
    {
        $ref = new ReflectionProperty($wrapper, '_params');
        $ref->setAccessible(true);

        return $ref->getValue($wrapper);
    }
}
