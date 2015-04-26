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
            require_once($versionFile->getRealPath());
            if (!class_exists($className)) {
                throw MigratorException::couldNotIntantiateVersionClass($className);
            }
            $version = new $className();

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
