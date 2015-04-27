<?php
/*
 * This file is part of the <package> package.
 *
 * (c) 2011-2015 Daniel Leech 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DTL\PhpcrMigrations;

use DTL\PhpcrMigrations\VersionStorage;
use DTL\PhpcrMigrations\VersionFinder;
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
    )
    {
        $this->storage = $storage;
        $this->finder = $finder;
        $this->session = $session;
    }

    public function getMigrator()
    {
        return new Migrator($this->session, $this->finder->getCollection(), $this->storage);
    }
}
