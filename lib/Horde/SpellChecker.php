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

use Horde\SpellChecker\SuggestMode;

/**
 * Provides a unified spellchecker API.
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2005-2026 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   SpellChecker
 */
abstract class Horde_SpellChecker
{
    public const SUGGEST_FAST = 1;
    public const SUGGEST_NORMAL = 2;
    public const SUGGEST_SLOW = 3;

    /**
     * @var array
     */
    protected $_params = [
        'html' => false,
        'locale' => 'en',
        'localDict' => [],
        'maxSuggestions' => 10,
        'minLength' => 3,
        'suggestMode' => self::SUGGEST_FAST,
    ];

    /**
     * @deprecated
     */
    public static function factory($driver, $params = [])
    {
        $class = 'Horde_SpellChecker_' . Horde_String::ucfirst(basename($driver));
        if (class_exists($class)) {
            return new $class($params);
        }

        throw new Horde_Exception('Driver ' . $driver . ' not found');
    }

    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    public function setParams($params)
    {
        $this->_params = array_merge($this->_params, $params);
    }

    /**
     * @return array
     */
    abstract public function spellCheck($text);

    /**
     * @return array
     */
    protected function _getWords($text)
    {
        return array_keys(array_flip(preg_split('/[\s\[\]]+/s', $text, -1, PREG_SPLIT_NO_EMPTY)));
    }

    protected function _inLocalDictionary($word)
    {
        return empty($this->_params['localDict'])
            ? false
            : in_array(Horde_String::lower($word, true, 'UTF-8'), $this->_params['localDict']);
    }

    protected function resolveSuggestMode(): SuggestMode
    {
        $mode = $this->_params['suggestMode'] ?? self::SUGGEST_FAST;
        if ($mode instanceof SuggestMode) {
            return $mode;
        }

        return SuggestMode::from((int) $mode);
    }
}
