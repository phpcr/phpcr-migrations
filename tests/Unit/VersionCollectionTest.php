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
use PHPCR\Migrations\VersionInterface;
use PHPUnit\Framework\TestCase;

class VersionCollectionTest extends TestCase
{
    public const VERSION1 = '201501010000';
    public const VERSION2 = '201501020000';
    public const VERSION3 = '201501030000';

    public function setUp(): void
    {
        $this->version1 = $this->createMock(VersionInterface::class);
        $this->version2 = $this->createMock(VersionInterface::class);
        $this->version3 = $this->createMock(VersionInterface::class);
    }

    /**
     * It knows if it contains a version.
     */
    public function testHas(): void
    {
        $collection = $this->createCollection([
            self::VERSION1 => $this->version1,
            self::VERSION3 => $this->version3,
        ]);
        $this->assertTrue($collection->has(self::VERSION1));
        $this->assertTrue($collection->has(self::VERSION3));
        $this->assertFalse($collection->has(self::VERSION2));
    }

    /**
     * It returns the versions required to migrate from up from A to B.
     */
    public function testFromAToBUp(): void
    {
        $collection = $this->createCollection([
            self::VERSION1 => $this->version1,
            self::VERSION2 => $this->version2,
            self::VERSION3 => $this->version3,
        ]);

        $versions = $collection->getVersions(self::VERSION1, self::VERSION3);

        $this->assertEquals([
            self::VERSION2, self::VERSION3,
        ], array_map('strval', array_keys($versions)));
    }

    /**
     * It returns the versions required to migrate down from A to B.
     */
    public function testDownFromAToBUp(): void
    {
        $collection = $this->createCollection([
            self::VERSION1 => $this->version1,
            self::VERSION2 => $this->version2,
            self::VERSION3 => $this->version3,
        ]);

        $versions = $collection->getVersions(self::VERSION3, self::VERSION1);

        $this->assertEquals([
            self::VERSION3, self::VERSION2,
        ], array_map('strval', array_keys($versions)));
    }

    private function createCollection(array $versions): VersionCollection
    {
        return new VersionCollection($versions);
    }
}
