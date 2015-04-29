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

use PHPCR\Migrations\Migrator;
use PHPCR\Migrations\MigratorFactory;
use PHPCR\Migrations\VersionCollection;
use PHPCR\Migrations\VersionFinder;
use PHPCR\Migrations\VersionStorage;
use PHPCR\SessionInterface;

class MigratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $storage = $this->prophesize('PHPCR\Migrations\VersionStorage');
        $finder = $this->prophesize('PHPCR\Migrations\VersionFinder');
        $session = $this->prophesize('PHPCR\SessionInterface');
        $finder->getCollection()->willReturn($this->prophesize('PHPCR\Migrations\VersionCollection')->reveal());

        $factory = new MigratorFactory(
            $storage->reveal(),
            $finder->reveal(),
            $session->reveal()
        );
        $migrator = $factory->getMigrator();
        $this->assertInstanceOf('PHPCR\Migrations\Migrator', $migrator);
    }
}
