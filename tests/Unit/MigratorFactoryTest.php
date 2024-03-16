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

use PHPCR\Migrations\MigratorFactory;
use PHPUnit\Framework\TestCase;

class MigratorFactoryTest extends TestCase
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
