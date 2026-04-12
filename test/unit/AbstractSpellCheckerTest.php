<?php

declare(strict_types=1);

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker\Test\Unit;

use Horde\SpellChecker\AbstractSpellChecker;
use Horde\SpellChecker\SpellCheckResult;
use Horde\SpellChecker\SuggestMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractSpellChecker::class)]
class AbstractSpellCheckerTest extends TestCase
{
    private ConcreteTestSpellChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new ConcreteTestSpellChecker();
    }

    public function testDefaultConstructorValues(): void
    {
        $values = $this->checker->exposedValues();

        $this->assertFalse($values['html']);
        $this->assertSame('en', $values['locale']);
        $this->assertSame([], $values['localDict']);
        $this->assertSame(10, $values['maxSuggestions']);
        $this->assertSame(3, $values['minLength']);
        $this->assertSame(SuggestMode::Fast, $values['suggestMode']);
    }

    public function testCustomConstructorValues(): void
    {
        $checker = new ConcreteTestSpellChecker(
            html: true,
            locale: 'de',
            localDict: ['horde'],
            maxSuggestions: 5,
            minLength: 2,
            suggestMode: SuggestMode::Slow,
        );
        $values = $checker->exposedValues();

        $this->assertTrue($values['html']);
        $this->assertSame('de', $values['locale']);
        $this->assertSame(['horde'], $values['localDict']);
        $this->assertSame(5, $values['maxSuggestions']);
        $this->assertSame(2, $values['minLength']);
        $this->assertSame(SuggestMode::Slow, $values['suggestMode']);
    }

    public function testGetWordsBasic(): void
    {
        $this->assertSame(
            ['hello', 'world', 'foo'],
            $this->checker->exposedGetWords('hello world foo'),
        );
    }

    public function testGetWordsSplitsOnBrackets(): void
    {
        $this->assertSame(
            ['some', 'bracketed', 'text'],
            $this->checker->exposedGetWords('some [bracketed] text'),
        );
    }

    public function testGetWordsRemovesDuplicates(): void
    {
        $this->assertSame(
            ['hello', 'world'],
            $this->checker->exposedGetWords('hello hello world'),
        );
    }

    public function testGetWordsEmpty(): void
    {
        $this->assertSame([], $this->checker->exposedGetWords(''));
    }

    public function testInLocalDictionaryFinds(): void
    {
        $checker = new ConcreteTestSpellChecker(localDict: ['foo', 'bar']);
        $this->assertTrue($checker->exposedInLocalDictionary('foo'));
        $this->assertTrue($checker->exposedInLocalDictionary('bar'));
    }

    public function testInLocalDictionaryMiss(): void
    {
        $checker = new ConcreteTestSpellChecker(localDict: ['foo']);
        $this->assertFalse($checker->exposedInLocalDictionary('baz'));
    }

    public function testInLocalDictionaryEmpty(): void
    {
        $this->assertFalse($this->checker->exposedInLocalDictionary('anything'));
    }
}

class ConcreteTestSpellChecker extends AbstractSpellChecker
{
    public function spellCheck(string $text): SpellCheckResult
    {
        return new SpellCheckResult();
    }

    /** @return array<string, mixed> */
    public function exposedValues(): array
    {
        return [
            'html' => $this->html,
            'locale' => $this->locale,
            'localDict' => $this->localDict,
            'maxSuggestions' => $this->maxSuggestions,
            'minLength' => $this->minLength,
            'suggestMode' => $this->suggestMode,
        ];
    }

    /** @return list<string> */
    public function exposedGetWords(string $text): array
    {
        return $this->getWords($text);
    }

    public function exposedInLocalDictionary(string $word): bool
    {
        return $this->inLocalDictionary($word);
    }
}
