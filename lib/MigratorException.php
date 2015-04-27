<?php
/*
 * This file is part of the <package> package.
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DTL\PhpcrMigrations;

class MigratorException extends \Exception
{
    public static function noMigrationsToExecute()
    {
        return new self('No migrations to execute');
    }

    public static function unknownVersion($version)
    {
        return new self(sprintf('Unknown version "%s"', $version));
    }

    public static function couldNotIntantiateVersionClass($className)
    {
        return new self(sprintf('Could not instantiate version class "%s", it does not exist', $className));
    }

    public static function versionNotInstance($className)
    {
        return new self(sprintf('Version class "%s" is not an instance of DTL\PhpcrMigrations\VersionInterface', $className));
    }

    public static function cannotInitializeAlreadyHasVersions()
    {
        return new self('Cannot initiaialize a content repository with previously existing migrations.');
    }

    public static function noClassesInVersionFile($file)
    {
        return new self(sprintf('No classes found in version file "%s"', $file));
    }

    public static function moreThanOneClassInVersionFile($file)
    {
        return new self(sprintf('More than one class found in version file "%s"', $file));
    }

}
