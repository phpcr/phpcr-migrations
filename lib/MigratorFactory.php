<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Migrations;

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
