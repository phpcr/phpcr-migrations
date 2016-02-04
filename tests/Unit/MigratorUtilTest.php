<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Migrations\tests\Unit;

use PHPCR\Migrations\MigratorUtil;

class MigratorUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * It should return the classname of a file.
     */
    public function testGetClassName()
    {
        $className = MigratorUtil::getClassNameFromFile(__DIR__ . '/migrations/Version201511240843.php');
        $this->assertEquals('\Sulu\Bundle\ContentBundle\Version201511240843', $className);
    }
}
