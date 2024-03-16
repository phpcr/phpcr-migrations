<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Migrations\Tests\Unit;

use PHPCR\Migrations\VersionCollection;
use PHPCR\Migrations\VersionFinder;
use PHPUnit\Framework\TestCase;

class VersionFinderTest extends TestCase
{
    private VersionFinder $finder;

    public function setUp(): void
    {
        $this->finder = new VersionFinder([
            __DIR__.'/../migrations',
        ]);
    }

    /**
     * It should return all version classes.
     *
     * @runInSeparateProcess
     */
    public function testGetCollection(): void
    {
        $collection = $this->finder->getCollection();
        $this->assertInstanceOf(VersionCollection::class, $collection);
        $versions = $collection->getAllVersions();
        $this->assertCount(3, $versions);
    }

    /**
     * It should do nothing if no migrations paths are given.
     */
    public function testNoMigrationPaths(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No paths were provided');
        new VersionFinder([]);
    }
}
