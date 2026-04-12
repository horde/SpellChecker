<?php

declare(strict_types=1);

/**
 * Copyright 2005-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker;

use Horde_String;

abstract class AbstractSpellChecker implements SpellCheckerInterface
{
    public function __construct(
        protected bool $html = false,
        protected string $locale = 'en',
        protected array $localDict = [],
        protected int $maxSuggestions = 10,
        protected int $minLength = 3,
        protected SuggestMode $suggestMode = SuggestMode::Fast,
    ) {}

    /**
     * @return list<string>
     */
    protected function getWords(string $text): array
    {
        return array_keys(
            array_flip(
                preg_split('/[\s\[\]]+/s', $text, -1, PREG_SPLIT_NO_EMPTY) ?: []
            )
        );
    }

    protected function inLocalDictionary(string $word): bool
    {
        if ($this->localDict === []) {
            return false;
        }

        return in_array(
            Horde_String::lower($word, true, 'UTF-8'),
            $this->localDict,
            true,
        );
    }
}
