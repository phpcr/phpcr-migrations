<?php

namespace DTL\PhpcrMigrations\tests\Unit;

use DTL\PhpcrMigrations\Migrator;
use DTL\PhpcrMigrations\MigratorFactory;
use DTL\PhpcrMigrations\VersionCollection;
use DTL\PhpcrMigrations\VersionFinder;
use DTL\PhpcrMigrations\VersionStorage;
use PHPCR\SessionInterface;

class MigratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $storage = $this->prophesize(VersionStorage::class);
        $finder = $this->prophesize(VersionFinder::class);
        $session = $this->prophesize(SessionInterface::class);
        $finder->getCollection()->willReturn($this->prophesize(VersionCollection::class)->reveal());

        $factory = new MigratorFactory(
            $storage->reveal(),
            $finder->reveal(),
            $session->reveal()
        );
        $migrator = $factory->getMigrator();
        $this->assertInstanceOf(Migrator::class, $migrator);
    }
}
