<?php

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
        $this->assertInstanceOf(VersionCollection::class, $collection);
        $versions = $collection->getAllVersions();
        $this->assertCount(3, $versions);
    }
}
