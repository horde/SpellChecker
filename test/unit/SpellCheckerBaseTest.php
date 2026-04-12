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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_SpellChecker::class)]
class SpellCheckerBaseTest extends TestCase
{
    private ConcreteSpellChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new ConcreteSpellChecker();
    }

    public function testDefaultParams(): void
    {
        $params = $this->checker->getParams();
        $this->assertFalse($params['html']);
        $this->assertSame('en', $params['locale']);
        $this->assertSame([], $params['localDict']);
        $this->assertSame(10, $params['maxSuggestions']);
        $this->assertSame(3, $params['minLength']);
        $this->assertSame(Horde_SpellChecker::SUGGEST_FAST, $params['suggestMode']);
    }

    public function testSetParamsMergesWithDefaults(): void
    {
        $this->checker->setParams(['locale' => 'de', 'html' => true]);

        $params = $this->checker->getParams();
        $this->assertSame('de', $params['locale']);
        $this->assertTrue($params['html']);
        $this->assertSame(10, $params['maxSuggestions']);
    }

    public function testGetWordsExtractsFromText(): void
    {
        $words = $this->checker->exposedGetWords('hello world foo');
        $this->assertSame(['hello', 'world', 'foo'], $words);
    }

    public function testGetWordsSplitsOnBrackets(): void
    {
        $words = $this->checker->exposedGetWords('some [bracketed] text');
        $this->assertSame(['some', 'bracketed', 'text'], $words);
    }

    public function testGetWordsRemovesDuplicates(): void
    {
        $words = $this->checker->exposedGetWords('hello hello world');
        $this->assertSame(['hello', 'world'], $words);
    }

    public function testGetWordsHandlesEmptyString(): void
    {
        $words = $this->checker->exposedGetWords('');
        $this->assertSame([], $words);
    }

    public function testInLocalDictionaryFindsWord(): void
    {
        $this->checker->setParams(['localDict' => ['foo', 'bar']]);
        $this->assertTrue($this->checker->exposedInLocalDictionary('foo'));
        $this->assertTrue($this->checker->exposedInLocalDictionary('bar'));
    }

    public function testInLocalDictionaryReturnsFalseForUnknown(): void
    {
        $this->checker->setParams(['localDict' => ['foo']]);
        $this->assertFalse($this->checker->exposedInLocalDictionary('baz'));
    }

    public function testInLocalDictionaryEmptyDict(): void
    {
        $this->assertFalse($this->checker->exposedInLocalDictionary('anything'));
    }

    public function testConstants(): void
    {
        $this->assertSame(1, Horde_SpellChecker::SUGGEST_FAST);
        $this->assertSame(2, Horde_SpellChecker::SUGGEST_NORMAL);
        $this->assertSame(3, Horde_SpellChecker::SUGGEST_SLOW);
    }
}

class ConcreteSpellChecker extends Horde_SpellChecker
{
    public function spellCheck($text): array
    {
        return ['bad' => [], 'suggestions' => []];
    }

    /** @return array<string, mixed> */
    public function getParams(): array
    {
        return $this->_params;
    }

    /** @return list<string> */
    public function exposedGetWords(string $text): array
    {
        return $this->_getWords($text);
    }

    public function exposedInLocalDictionary(string $word): bool
    {
        return $this->_inLocalDictionary($word);
    }
}
