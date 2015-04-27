PHPCR Migrations
================

Migrations library for PHPCR influenced by [Doctrine
migrations](https://github.com/doctrine/migrations).

For integration with Symfony see the [PHPCR Migrations
Bundle](https://github.com/dantleech/phpcr-migrations-bundle).

Usage
-----

````php
$storage = new VersionStorage($phpcrSession);
$finder = new VersionFinder(array('/path/to/migrations'));

$versions = $finder->getVersionCollection();
$migrator = new Migrator($session, $versionCollection, $storage);

$to = '201504241744';
$migrator->migrate($to);
````

Versions
--------

Version classes contain `up` and `down` methods. The class is quite simple:

````php
<?php

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
