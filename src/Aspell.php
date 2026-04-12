<?php

declare(strict_types=1);

/**
 * Copyright 2005-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\SpellChecker;

use Horde\SpellChecker\Exception\SpellCheckException;

class Aspell extends AbstractSpellChecker
{
    public function __construct(
        protected string $path = 'aspell',
        bool $html = false,
        string $locale = 'en',
        array $localDict = [],
        int $maxSuggestions = 10,
        int $minLength = 3,
        SuggestMode $suggestMode = SuggestMode::Fast,
    ) {
        parent::__construct($html, $locale, $localDict, $maxSuggestions, $minLength, $suggestMode);
    }

    public function spellCheck(string $text): SpellCheckResult
    {
        if ($this->html) {
            $input = strtr($text, "\n", ' ');
        } else {
            $words = $this->getWords($text);
            if ($words === []) {
                return new SpellCheckResult();
            }
            $input = implode(' ', $words);
        }

        $cmd = $this->buildCommand();

        $descspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descspec, $pipes);
        if (!is_resource($process)) {
            throw new SpellCheckException('Spellcheck failed. Command line: ' . $cmd);
        }

        fwrite($pipes[0], '^' . $input);
        fclose($pipes[0]);

        $out = '';
        while (!feof($pipes[1])) {
            $out .= fread($pipes[1], 8192);
        }
        fclose($pipes[1]);

        $err = '';
        while (!feof($pipes[2])) {
            $err .= fread($pipes[2], 8192);
        }
        fclose($pipes[2]);

        proc_close($process);

        if ($out === '') {
            throw new SpellCheckException('Spellcheck failed. Command line: ' . $cmd);
        }

        return $this->parseOutput($out);
    }

    /**
     * @internal Visible for testing
     */
    public function buildCommand(): string
    {
        $args = ['-a', '--encoding=UTF-8'];

        $args[] = match ($this->suggestMode) {
            SuggestMode::Fast => '--sug-mode=fast',
            SuggestMode::Slow => '--sug-mode=bad-spellers',
            SuggestMode::Normal => '--sug-mode=normal',
        };

        $args[] = '--lang=' . escapeshellarg($this->locale);
        $args[] = '--ignore=' . escapeshellarg((string) max($this->minLength - 1, 0));

        if ($this->html) {
            $args[] = '-H';
            $args[] = '--rem-html-check=alt';
        }

        return escapeshellcmd($this->path) . ' ' . implode(' ', $args);
    }

    private function parseOutput(string $out): SpellCheckResult
    {
        $bad = [];
        $suggestions = [];

        foreach (array_map('trim', explode("\n", $out)) as $line) {
            if ($line === '') {
                continue;
            }

            $parts = explode(' ', $line, 3);
            $word = $parts[1] ?? null;

            if ($word === null
                || $this->inLocalDictionary($word)
                || in_array($word, $bad, true)
            ) {
                continue;
            }

            switch ($line[0]) {
                case '#':
                    $bad[] = $word;
                    $suggestions[] = [];
                    break;

                case '&':
                    $bad[] = $word;
                    $suggestions[] = array_slice(
                        explode(', ', substr($line, strpos($line, ':') + 2)),
                        0,
                        $this->maxSuggestions,
                    );
                    break;
            }
        }

        return new SpellCheckResult($bad, $suggestions);
    }
}
