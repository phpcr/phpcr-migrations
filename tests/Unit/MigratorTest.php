<?php

namespace DTL\PhpcrMigrations\Tests\Unit;

use PHPCR\SessionInterface;
use DTL\PhpcrMigrator\PhpcrMigrator;
use DTL\PhpcrMigrations\VersionStorage;
use DTL\PhpcrMigrations\VersionCollection;
use Symfony\Component\Console\Output\OutputInterface;
use DTL\PhpcrMigrations\MigratorException;
use DTL\PhpcrMigrations\Migrator;
use DTL\PhpcrMigrations\VersionInterface;

class MigratorTest extends \PHPUnit_Framework_TestCase
{
    const VERSION1 = '201501010000';
    const VERSION2 = '201501010010';

    public function setUp()
    {
        $this->session = $this->prophesize(SessionInterface::class);
        $this->versionCollection = $this->prophesize(VersionCollection::class);
        $this->versionStorage = $this->prophesize(VersionStorage::class);
        $this->output = $this->prophesize(OutputInterface::class);
        $this->version1 = $this->prophesize(VersionInterface::class);
        $this->version2 = $this->prophesize(VersionInterface::class);

        $this->migrator = new Migrator(
            $this->session->reveal(),
            $this->versionCollection->reveal(),
            $this->versionStorage->reveal()
        );
    }

    /**
     * It should throw an exception if the requested migration does not exist
     *
     * @expectedException DTL\PhpcrMigrations\MigratorException
     */
    public function testMigrateNonExisting()
    {
        $version = '20001212121212';
        $this->versionCollection->has($version)->willReturn(false);
        $this->migrator->migrate($version, $this->output->reveal());
    }

    /**
     * It should migrate up to the latest version
     */
    public function testMigrateLatestVersion()
    {
        $this->versionCollection->getLatestVersion()->willReturn(self::VERSION2);
        $this->versionCollection->has(self::VERSION2)->willReturn(true);
        $this->versionStorage->getCurrentVersion()->willReturn(self::VERSION1);
        $this->versionCollection->getVersions(self::VERSION1, self::VERSION2, 'up')
            ->willReturn(array(
                self::VERSION2 => $this->version2->reveal(),
            ));
        $this->version2->up($this->session->reveal())->shouldBeCalled();
        $this->versionStorage->add(self::VERSION2)->shouldBeCalled();

        $this->migrator->migrate(null, $this->output->reveal());
    }

    /**
     * It should migrate down to the given version
     */
    public function testMigrateDownFromLatestVersion()
    {
        $this->versionCollection->has(self::VERSION1)->willReturn(true);
        $this->versionStorage->getCurrentVersion()->willReturn(self::VERSION2);
        $this->versionStorage->getPersistedVersions()->willReturn(array(
            self::VERSION1, self::VERSION2
        ));
        $this->versionCollection->getVersions(self::VERSION2, self::VERSION1, 'down')
            ->willReturn(array(
                self::VERSION2 => $this->version2->reveal(),
            ));
        $this->version1->down($this->session->reveal())->shouldNotBeCalled();
        $this->version2->down($this->session->reveal())->shouldBeCalled();
        $this->versionStorage->remove(self::VERSION2)->shouldBeCalled();

        $this->migrator->migrate(self::VERSION1, $this->output->reveal());
    }

    /**
     * It should throw an exception if there are no versions to migrate
     *
     * @expectedException DTL\PhpcrMigrations\MigratorException
     */
    public function testMigrateNoMigrations()
    {
        $this->versionCollection->getLatestVersion()->willReturn(self::VERSION2);
        $this->versionCollection->has(self::VERSION2)->willReturn(true);
        $this->versionStorage->getCurrentVersion()->willReturn(self::VERSION1);
        $this->versionStorage->getPersistedVersions()->willReturn(array());
        $this->versionCollection->getVersions(self::VERSION1, self::VERSION2, 'up')
            ->willReturn(array(
            ));

        $this->migrator->migrate(null, $this->output->reveal());
    }
}
