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

class VersionCollection
{
    private $versions;

    public function __construct(array $versions)
    {
        ksort($versions, SORT_STRING);
        $this->versions = $versions;
    }

    public function has($versionTimestamp)
    {
        return isset($this->versions[$versionTimestamp]);
    }

    public function getAllVersions()
    {
        return $this->versions;
    }

    public function getVersions($from, $to)
    {
        $direction = $from > $to ? 'down' : 'up';
        $result = [];
        $versions = $this->versions;

        if ($from == $to) {
            return [];
        }

        if ('up' === $direction) {
            ksort($versions, SORT_STRING);
        } else {
            krsort($versions, SORT_STRING);
        }

        $found = null === $from ? true : false;
        foreach ($versions as $timestamp => $version) {
            if ($timestamp == $from) {
                $found = true;

                if ('down' == $direction) {
                    $result[$timestamp] = $version;
                }

                continue;
            }

            if (false === $found) {
                continue;
            }

            if ($timestamp == $to) {
                if ('up' == $direction) {
                    $result[$timestamp] = $version;
                }
                break;
            }

            $result[$timestamp] = $version;
        }

        return $result;
    }

    public function getLatestVersion()
    {
        end($this->versions);

        return key($this->versions);
    }

    /**
     * Return the version after the given version.
     *
     * @param string|null $from
     */
    public function getNextVersion($from)
    {
        $found = false;
        foreach (array_keys($this->versions) as $timestamp) {
            if (null === $from) {
                return $timestamp;
            }

            if ($timestamp == $from) {
                $found = true;
                continue;
            }

            if ($found) {
                return $timestamp;
            }
        }
    }

    /**
     * Return the version before the given version.
     *
     * @param string $from
     */
    public function getPreviousVersion($from)
    {
        $lastTimestamp = 0;
        foreach (array_keys($this->versions) as $timestamp) {
            if ($timestamp == $from) {
                return $lastTimestamp;
            }
            $lastTimestamp = $timestamp;
        }

        return 0;
    }
}
