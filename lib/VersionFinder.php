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

use Symfony\Component\Finder\Finder;

class VersionFinder
{
    private $paths;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    public function getCollection()
    {
        $versions = array();
        $finder = new Finder();
        $finder->name('/Version[0-9]{12}.php/');
        $finder->files();

        foreach ($this->paths as $path) {
            $finder->in($path);
        }

        foreach ($finder as $versionFile) {
            $className = $versionFile->getBasename('.php');
            $declaredClasses = get_declared_classes();
            require_once($versionFile->getRealPath());
            $newClasses = array_diff(get_declared_classes(), $declaredClasses);

            if (count($newClasses) === 0) {
                throw MigratorException::noClassesInVersionFile($versionFile->getBaseName());
            }

            if (count($newClasses) !== 1) {
                throw MigratorException::moreThanOneClassInVersionFile($versionFile->getBaseName());
            }

            $classFqn = reset($newClasses);

            $version = new $classFqn();

            if (!$version instanceof VersionInterface) {
                throw MigratorException::versionNotInstance($className);
            }

            $versionTimestamp = substr($versionFile->getBaseName(), 7, 12);
            $versions['V'.$versionTimestamp] = $version;
        }

        $collection = new VersionCollection($versions);

        return $collection;
    }
}
