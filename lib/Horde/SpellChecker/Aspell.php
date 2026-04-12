<?php

/**
 * Copyright 2005-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2005-2026 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   SpellChecker
 */

use Horde\SpellChecker\Aspell;
use Horde\SpellChecker\Exception\SpellCheckException;

/**
 * A spellcheck driver for the aspell/ispell binary.
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2005-2026 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   SpellChecker
 */
class Horde_SpellChecker_Aspell extends Horde_SpellChecker
{
    private ?Aspell $_delegate = null;

    /**
     * @param array $args  Additional arguments:
     *   - path: (string) Path to the aspell binary.
     */
    public function __construct(array $args = [])
    {
        parent::__construct(array_merge([
            'path' => 'aspell',
        ], $args));
    }

    public function setParams($params)
    {
        parent::setParams($params);
        $this->_delegate = null;
    }

    public function spellCheck($text)
    {
        try {
            return $this->getDelegate()->spellCheck($text)->toArray();
        } catch (SpellCheckException $e) {
            throw new Horde_SpellChecker_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function getDelegate(): Aspell
    {
        if ($this->_delegate === null) {
            $this->_delegate = new Aspell(
                path: $this->_params['path'] ?? 'aspell',
                html: (bool) ($this->_params['html'] ?? false),
                locale: (string) ($this->_params['locale'] ?? 'en'),
                localDict: (array) ($this->_params['localDict'] ?? []),
                maxSuggestions: (int) ($this->_params['maxSuggestions'] ?? 10),
                minLength: (int) ($this->_params['minLength'] ?? 3),
                suggestMode: $this->resolveSuggestMode(),
            );
        }

        return $this->_delegate;
    }
}
