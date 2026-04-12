<?php

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   SpellChecker
 */

/**
 * Exception handler for the Horde_SpellChecker package.
 *
 * Extends Horde_Exception_Wrapped for BC. Note: Horde_Exception_Wrapped
 * accepts an Exception/PEAR_Error as $message (first arg) to preserve the
 * chain. Passing $previous as a third arg is silently ignored — this is a
 * known limitation of the Wrapped constructor, kept for BC (wontfix).
 *
 * New code should use Horde\SpellChecker\Exception\SpellCheckException
 * (based on HordeRuntimeException) instead.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2026 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   SpellChecker
 */
class Horde_SpellChecker_Exception extends Horde_Exception_Wrapped {}
