PHPCR Migrations
================

[![Build
Status](https://travis-ci.org/dantleech/phpcr-migrations.svg?branch=master)](https://travis-ci.org/dantleech/phpcr-migrations)

Migrations library for PHPCR influenced by [Doctrine
migrations](https://github.com/doctrine/migrations).

For integration with Symfony see the [PHPCR Migrations
Bundle](https://github.com/dantleech/phpcr-migrations-bundle).

Usage
-----

````php
<?php

use Symfony\Component\Console\Output\NullOutput;
use DTL\PhpcrMigrations\VersionStorage;
use DTL\PhpcrMigrations\VersionFinder;
use DTL\PhpcrMigrations\Migrator;

$storage = new VersionStorage($phpcrSession);
$finder = new VersionFinder(array('/path/to/migrations'));

$versions = $finder->getVersionCollection();
$migrator = new Migrator($session, $versionCollection, $storage);

$to = '201504241744';
$output = new NullOutput();
$migrator->migrate($to, $output);
````

Versions
--------

Version classes contain `up` and `down` methods. The class is quite simple:

````php
<?php

use DTL\PhpcrMigrations\VersionInterface;

class Version201504241200 implements VersionInterface
{
    public function up(SessionInterface $session)
    {
        $session->doSomething();
    }

    public function down(SessionInterface $session)
    {
        $session->undoSomething();
    }
}
````

They must be named `VersionYYYMMDDHHMM`. If they are not so named, then they
will not be detected.
