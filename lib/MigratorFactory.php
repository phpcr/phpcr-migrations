<?php

namespace DTL\PhpcrMigrations;

use PHPCR\SessionInterface;

class MigratorFactory
{
    private $storage;
    private $finder;
    private $session;

    public function __construct(
        VersionStorage $storage,
        VersionFinder $finder,
        SessionInterface $session
    ) {
        $this->storage = $storage;
        $this->finder = $finder;
        $this->session = $session;
    }

    public function getMigrator()
    {
        return new Migrator($this->session, $this->finder->getCollection(), $this->storage);
    }
}
