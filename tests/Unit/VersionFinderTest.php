<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DTL\PhpcrMigrations\tests\Unit;

use DTL\PhpcrMigrations\VersionCollection;
use DTL\PhpcrMigrations\VersionFinder;

class VersionFinderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->finder = new VersionFinder(array(
            __DIR__ . '/../migrations',
        ));
    }

    /**
     * It should return all version classes.
     *
     * @runInSeparateProcess
     */
    public function testGetCollection()
    {
        $collection = $this->finder->getCollection();
        $this->assertInstanceOf('DTL\PhpcrMigrations\VersionCollection', $collection);
        $versions = $collection->getAllVersions();
        $this->assertCount(3, $versions);
    }
}
