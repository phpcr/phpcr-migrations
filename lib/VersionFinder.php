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
use DTL\PhpcrMigrations\Util;

class VersionFinder
{
    private $paths;
    private $collection;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    public function getCollection()
    {
        if ($this->collection) {
            return $this->collection;
        }

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
            $classFqn = Util::getClassNameFromFile($versionFile->getRealPath());

            $version = new $classFqn();

            if (!$version instanceof VersionInterface) {
                throw MigratorException::versionNotInstance($className);
            }

            $versionTimestamp = substr($versionFile->getBaseName(), 7, 12);
            $versions[$versionTimestamp] = $version;
        }

        $this->collection = new VersionCollection($versions);

        return $this->collection;
    }
}
