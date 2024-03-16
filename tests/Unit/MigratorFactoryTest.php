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

use PHPCR\Migrations\Migrator;
use PHPCR\Migrations\MigratorFactory;
use PHPCR\Migrations\VersionCollection;
use PHPCR\Migrations\VersionFinder;
use PHPCR\Migrations\VersionStorage;
use PHPCR\SessionInterface;
use PHPUnit\Framework\TestCase;

class MigratorFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $storage = $this->createMock(VersionStorage::class);
        $finder = $this->createMock(VersionFinder::class);
        $finder->expects($this->once())
            ->method('getCollection')
            ->willReturn($this->createMock(VersionCollection::class))
        ;
        $session = $this->createMock(SessionInterface::class);

        $factory = new MigratorFactory($storage, $finder, $session);
        $migrator = $factory->getMigrator();
        $this->assertInstanceOf(Migrator::class, $migrator);
    }
}
