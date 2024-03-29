<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Migrations\Tests\Functional;

use PHPCR\Migrations\Exception\MigratorException;
use PHPCR\Migrations\Migrator;
use PHPCR\Migrations\Tests\BaseTestCase;
use PHPCR\Migrations\VersionFinder;
use PHPCR\Migrations\VersionStorage;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;

class MigrationTest extends BaseTestCase
{
    public const VERSION1 = '201501011200';
    public const VERSION2 = '201501011212';
    public const VERSION3 = '201501011215';

    private BufferedOutput $output;
    private Filesystem $filesystem;
    private string $migrationDistDir;
    private string $migrationDir;
    private VersionStorage $storage;

    public function setUp(): void
    {
        $this->initPhpcr();
        $this->migrationDir = __DIR__.'/../migrations';
        $this->migrationDistDir = __DIR__.'/../migrations.dist';
        $this->filesystem = new Filesystem();

        if (file_exists($this->migrationDir)) {
            $this->filesystem->remove($this->migrationDir);
        }

        mkdir($this->migrationDir);
        $this->output = new BufferedOutput();
    }

    /**
     * It should execute all the migrations and populate the versions table.
     */
    public function testMigration(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);

        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(3, $migratedVersions);

        $this->assertTrue($this->session->nodeExists('/hello/world'));
        $this->assertTrue($this->session->nodeExists('/hello/dan'));

        $nodes = $this->session->getNode('/phpcrmig:versions')->getNodes();
        $names = array_keys((array) $nodes);

        $this->assertContains(201501011200, $names);
        $this->assertContains(201501011212, $names);
        $this->assertContains(201501011215, $names);
    }

    /**
     * It should not run migrations that have already been executed.
     */
    public function testMigrateAgain(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);

        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(3, $migratedVersions);

        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(0, $migratedVersions);
    }

    /**
     * It should run new migrations.
     */
    public function testMigrateAdd(): void
    {
        $this->addVersion(self::VERSION1);

        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(1, $migratedVersions);

        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);

        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(2, $migratedVersions);
    }

    /**
     * It should run migrations backwards.
     */
    public function testMigrateDown(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);
        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(3, $migratedVersions);
        $this->assertTrue($this->session->nodeExists('/hello/dan'));

        // migrate down one version
        $migratedVersions = $this->getMigrator()->migrate(self::VERSION2, $this->output);
        $this->assertCount(1, $migratedVersions);
        $nodes = $this->session->getNode('/phpcrmig:versions')->getNodes();
        $names = array_keys((array) $nodes);
        $this->assertCount(2, $names);
        $this->assertNotContains(self::VERSION3, $names);

        $this->assertFalse($this->session->nodeExists('/hello/dan'));

        $migratedVersions = $this->getMigrator()->migrate(self::VERSION1, $this->output);
        $this->assertCount(1, $migratedVersions);

        $this->assertFalse($this->session->nodeExists('/hello/world'));

        $migratedVersions = $this->getMigrator()->migrate(self::VERSION1, $this->output);
        $this->assertCount(0, $migratedVersions);
    }

    /**
     * It should do nothing if target version is current version.
     */
    public function testMigrateToCurrentVersionFromCurrent(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $migratedVersions = $this->getMigrator()->migrate(self::VERSION2, $this->output);
        $this->assertCount(2, $migratedVersions);

        $migratedVersions = $this->getMigrator()->migrate(self::VERSION2, $this->output);
        $this->assertCount(0, $migratedVersions);
    }

    /**
     * It should add all migrations.
     */
    public function testInitialize(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->getMigrator()->initialize();

        $nodes = $this->session->getNode('/phpcrmig:versions')->getNodes();

        $this->assertCount(2, $nodes);
    }

    /**
     * It should throw an exception if trying to reiniitialize.
     */
    public function testReinitialize(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);

        $this->getMigrator()->initialize();

        $this->expectException(MigratorException::class);
        $this->expectExceptionMessage('Will not re-initialize');
        $this->getMigrator()->initialize();
    }

    /**
     * It should migrate to the next version.
     */
    public function testMigrateNext(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);
        $migratedVersions = $this->getMigrator()->migrate('up', $this->output);
        $this->assertCount(1, $migratedVersions);
        $this->assertEquals(self::VERSION1, $this->storage->getCurrentVersion());

        $migratedVersions = $this->getMigrator()->migrate('up', $this->output);
        $this->assertCount(1, $migratedVersions);
        $this->assertEquals(self::VERSION2, $this->storage->getCurrentVersion());
    }

    /**
     * It should migrate to the previous version.
     */
    public function testMigratePrevious(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);
        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(3, $migratedVersions);
        $this->assertEquals(self::VERSION3, $this->storage->getCurrentVersion());

        $migratedVersions = $this->getMigrator()->migrate('down', $this->output);
        $this->assertCount(1, $migratedVersions);
        $this->assertEquals(self::VERSION2, $this->storage->getCurrentVersion());

        $migratedVersions = $this->getMigrator()->migrate('down', $this->output);
        $this->assertCount(1, $migratedVersions);
        $this->assertEquals(self::VERSION1, $this->storage->getCurrentVersion());

        $migratedVersions = $this->getMigrator()->migrate('down', $this->output);
        $this->assertCount(1, $migratedVersions);

        $migratedVersions = $this->getMigrator()->migrate('down', $this->output);
        $this->assertCount(0, $migratedVersions);
    }

    /**
     * It should migrate to the top.
     */
    public function testMigrateTop(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);
        $migratedVersions = $this->getMigrator()->migrate('top', $this->output);
        $this->assertCount(3, $migratedVersions);
    }

    /**
     * It should migrate to the bottom.
     */
    public function testMigrateBottom(): void
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);
        $this->getMigrator()->migrate('top', $this->output);
        $migratedVersions = $this->getMigrator()->migrate('bottom', $this->output);
        $this->assertCount(3, $migratedVersions);
    }

    private function addVersion($version): void
    {
        $this->filesystem->copy(
            $this->migrationDistDir.'/Version'.$version.'.php',
            $this->migrationDir.'/Version'.$version.'.php'
        );
    }

    private function getMigrator(): Migrator
    {
        $this->storage = new VersionStorage($this->session);
        $finder = new VersionFinder([$this->migrationDir]);
        $versions = $finder->getCollection();

        return new Migrator($this->session, $versions, $this->storage);
    }
}
