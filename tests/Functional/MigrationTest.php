<?php

namespace DTL\PhpcrMigrations\Tests\Functional;

use DTL\PhpcrMigrations\VersionStorage;
use DTL\PhpcrMigrations\VersionFinder;
use DTL\PhpcrMigrations\Migrator;
use DTL\PhpcrMigrations\BaseTestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;

class MigrationTest extends BaseTestCase
{
    const VERSION1 = '201501011200';
    const VERSION2 = '201501011212';
    const VERSION3 = '201501011215';

    private $migrator;
    private $output;
    private $filesystem;
    private $migrationDir;

    public function setUp()
    {
        $this->initPhpcr();
        $this->migrationDir = __DIR__ . '/../migrations';
        $this->migrationDistDir = __DIR__ . '/../migrations.dist';
        $this->filesystem = new Filesystem();

        if (file_exists($this->migrationDir)) {
            $this->filesystem->remove($this->migrationDir);
        }

        mkdir($this->migrationDir);
        $this->output = new BufferedOutput();
    }

    /**
     * It should execute all the migrations and populate the versions table
     */
    public function testMigration()
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->addVersion(self::VERSION3);

        $migratedVersions = $this->getMigrator()->migrate(null, $this->output);
        $this->assertCount(3, $migratedVersions);

        $this->assertTrue($this->session->nodeExists('/hello/world'));
        $this->assertTrue($this->session->nodeExists('/hello/dan'));

        $nodes = $this->session->getNode('/phpcrMigrations:versions')->getNodes();
        $names = array_keys((array) $nodes);

        $this->assertContains('V201501011200', $names);
        $this->assertContains('V201501011212', $names);
        $this->assertContains('V201501011215', $names);
    }

    /**
     * It should not run migrations that have already been executed
     */
    public function testMigrateAgain()
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
     * It should run new migrations
     */
    public function testMigrateAdd()
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
     * It should run migrations backwards
     */
    public function testMigrateDown()
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
        $nodes = $this->session->getNode('/phpcrMigrations:versions')->getNodes();
        $names = array_keys((array) $nodes);
        $this->assertCount(2, $names);
        $this->assertNotContains('V' . self::VERSION3, $names);

        $this->assertFalse($this->session->nodeExists('/hello/dan'));

        $migratedVersions = $this->getMigrator()->migrate(self::VERSION1, $this->output);
        $this->assertCount(1, $migratedVersions);

        $this->assertFalse($this->session->nodeExists('/hello/world'));

        $migratedVersions = $this->getMigrator()->migrate(self::VERSION1, $this->output);
        $this->assertCount(0, $migratedVersions);
    }

    /**
     * It should do nothing if target version is current version
     */
    public function testMigrateToCurrentVersionFromCurrent()
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $migratedVersions = $this->getMigrator()->migrate(self::VERSION2, $this->output);
        $this->assertCount(2, $migratedVersions);

        $migratedVersions = $this->getMigrator()->migrate(self::VERSION2, $this->output);
        $this->assertCount(0, $migratedVersions);
    }

    /**
     * It should add all migrations
     */
    public function testInitialize()
    {
        $this->addVersion(self::VERSION1);
        $this->addVersion(self::VERSION2);
        $this->getMigrator()->initialize();

        $nodes = $this->session->getNode('/phpcrMigrations:versions')->getNodes();

        $this->assertCount(2, $nodes);
    }

    private function addVersion($version)
    {
        $this->filesystem->copy(
            $this->migrationDistDir . '/Version' . $version . '.php',
            $this->migrationDir . '/Version' . $version . '.php'
        );
    }

    private function getMigrator()
    {
        $storage = new VersionStorage($this->session);
        $finder = new VersionFinder(array($this->migrationDir));
        $versions = $finder->getCollection();
        $migrator = new Migrator($this->session, $versions, $storage);

        return $migrator;
    }
}
