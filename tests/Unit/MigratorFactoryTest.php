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
        $storage = $this->prophesize('DTL\PhpcrMigrations\VersionStorage');
        $finder = $this->prophesize('DTL\PhpcrMigrations\VersionFinder');
        $session = $this->prophesize('PHPCR\SessionInterface');
        $finder->getCollection()->willReturn($this->prophesize('DTL\PhpcrMigrations\VersionCollection')->reveal());

        $factory = new MigratorFactory(
            $storage->reveal(),
            $finder->reveal(),
            $session->reveal()
        );
        $migrator = $factory->getMigrator();
        $this->assertInstanceOf('DTL\PhpcrMigrations\Migrator', $migrator);
    }
}
