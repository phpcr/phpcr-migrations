<?php

namespace DTL\PhpcrMigrations\Tests\Unit;

use DTL\PhpcrMigrations\VersionFinder;
use DTL\PhpcrMigrations\VersionCollection;

class VersionFinderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->finder = new VersionFinder(array(
            __DIR__ . '/../migrations'
        ));
    }

    /**
     * It should return all version classes
     */
    public function testGetCollection()
    {
        $collection = $this->finder->getCollection();
        $this->assertInstanceOf(VersionCollection::class, $collection);
        $versions = $collection->getAllVersions();
        $this->assertCount(2, $versions);
    }

}
