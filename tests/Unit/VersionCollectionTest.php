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

use PHPCR\Migrations\VersionCollection;
use PHPCR\Migrations\VersionInterface;

class VersionCollectionTest extends \PHPUnit_Framework_TestCase
{
    const VERSION1 = '201501010000';
    const VERSION2 = '201501020000';
    const VERSION3 = '201501030000';

    public function setUp()
    {
        $this->version1 = $this->prophesize('PHPCR\Migrations\VersionInterface');
        $this->version2 = $this->prophesize('PHPCR\Migrations\VersionInterface');
        $this->version3 = $this->prophesize('PHPCR\Migrations\VersionInterface');
    }

    /**
     * It knows if it contains a version.
     */
    public function testHas()
    {
        $collection = $this->createCollection(array(
            self::VERSION1 => $this->version1->reveal(),
            self::VERSION3 => $this->version3->reveal(),
        ));
        $this->assertTrue($collection->has(self::VERSION1));
        $this->assertTrue($collection->has(self::VERSION3));
        $this->assertFalse($collection->has(self::VERSION2));
    }

    /**
     * It returns the versions required to migrate from up from A to B.
     */
    public function testFromAToBUp()
    {
        $collection = $this->createCollection(array(
            self::VERSION1 => $this->version1->reveal(),
            self::VERSION2 => $this->version2->reveal(),
            self::VERSION3 => $this->version3->reveal(),
        ));

        $versions = $collection->getVersions(self::VERSION1, self::VERSION3);

        $this->assertEquals(array(
            self::VERSION2, self::VERSION3,
        ), array_map('strval', array_keys($versions)));
    }

    /**
     * It returns the versions required to migrate down from A to B.
     */
    public function testDownFromAToBUp()
    {
        $collection = $this->createCollection(array(
            self::VERSION1 => $this->version1->reveal(),
            self::VERSION2 => $this->version2->reveal(),
            self::VERSION3 => $this->version3->reveal(),
        ));

        $versions = $collection->getVersions(self::VERSION3, self::VERSION1);

        $this->assertEquals(array(
            self::VERSION3, self::VERSION2,
        ), array_map('strval', array_keys($versions)));
    }

    private function createCollection($versions)
    {
        return new VersionCollection($versions);
    }
}
