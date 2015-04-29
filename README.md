PHPCR Migrations
================

[![Build
Status](https://travis-ci.org/phpcr/phpcr-migrations.svg?branch=master)](https://travis-ci.org/phpcr/phpcr-migrations)

Migrations library for PHPCR influenced by [Doctrine
migrations](https://github.com/doctrine/migrations).

For integration with Symfony see the [PHPCR Migrations
Bundle](https://github.com/dantleech/phpcr-migrations-bundle).

Usage
-----

````php
<?php

use Symfony\Component\Console\Output\NullOutput;
use PHPCR\Migrations\VersionStorage;
use PHPCR\Migrations\VersionFinder;
use PHPCR\Migrations\Migrator;

$storage = new VersionStorage($phpcrSession);
$finder = new VersionFinder(array('/path/to/migrations'));

$versions = $finder->getVersionCollection();
$migrator = new Migrator($session, $versionCollection, $storage);

$to = '201504241744';
$output = new NullOutput();
$migrator->migrate($to, $output);
````

You may also create a factory class (useful when you use dependency
injection):

````
<?php

$factory = new MigratorFactory($storage, $finder, $session);
$migrator = $factory->getMigrator();
````

### Initializing

When you install a project for the first time you need to initialize the
versions:

````
<?php

$migrator->initialize();
````

This should be part of your build process and it will add all the versions to
the migration version node in the content repository.

### Migrating


```php
$migrator->migrate('201501011200', $output); // migrate to a specific version
$migrator->migrate('up', $output); // migrate up a version
$migrator->migrate('down', $output); // migrate down a version
$migrator->migrate('top', $output); // migrate to the latest version
$migrator->migrate('bottom', $output); // revert all versions
````

### Listing versions

You can access information about available versions from the
`VersionCollection` object:


````php
$versionCollection->getAllVersions();
````

### Determining the current version

You can determine the current version from the `VersionStorage` object:

````php
$versionStroage->getCurrentVersion();
````

Version classes
---------------

Version classes contain `up` and `down` methods. The class is quite simple:

````php
<?php

use PHPCR\Migrations\VersionInterface;

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

The `down` method should revert any changes made in the `up` method. Always
check that revcerting your migration works.
