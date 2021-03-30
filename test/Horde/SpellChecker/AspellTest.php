<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  SpellChecker
 */
namespace Horde\SpellChecker;
use PHPUnit\Framework\TestCase;

/**
 * Tests for IMAP mailbox sorting.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @ignore
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  SpellChecker
 */
class AspellTest extends TestCase
{
    protected $aspell;

    public function setUp(): void
    {
        $aspell = trim(`which aspell`);
        if (!is_executable($aspell)) {
            $aspell = trim(`which ispell`);
        }

        if (!is_executable($aspell)) {
            $this->markTestSkipped('No aspell/ispell binary found.');
        }

        $this->aspell = new Horde_SpellChecker_Aspell(array(
            'path' => $aspell
        ));
    }

    public function testAspell()
    {
        $res = $this->aspell->spellCheck('some tet [mispeled] ?');

        $this->assertNotEmpty($res);
        $this->assertNotEmpty($res['bad']);
        $this->assertEquals(
            $res['bad'],
            array('tet', 'mispeled')
        );
        $this->assertNotEmpty($res['suggestions']);
        $this->assertNotEmpty($res['suggestions'][0]);
        $this->assertNotEmpty($res['suggestions'][1]);
    }

}
