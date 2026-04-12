<?php

declare(strict_types=1);

/**
 * Copyright 2005-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker;

class SpellCheckResult
{
    /**
     * @param list<string> $bad Misspelled words
     * @param list<list<string>> $suggestions Suggestions per misspelled word (parallel to $bad)
     */
    public function __construct(
        public readonly array $bad = [],
        public readonly array $suggestions = [],
    ) {}

    /**
     * @return array{bad: list<string>, suggestions: list<list<string>>}
     */
    public function toArray(): array
    {
        return [
            'bad' => $this->bad,
            'suggestions' => $this->suggestions,
        ];
    }
}
