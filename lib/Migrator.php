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

use PHPCR\SessionInterface;
use DTL\PhpcrMigrations\VersionStorage;
use DTL\PhpcrMigrations\VersionCollection;
use Symfony\Component\Console\Output\OutputInterface;

class Migrator
{
    private $session;
    private $versionCollection;
    private $versionStorage;

    public function __construct(
        SessionInterface $session,
        VersionCollection $versionCollection,
        VersionStorage $versionStorage
    )
    {
        $this->session = $session;
        $this->versionCollection = $versionCollection;
        $this->versionStorage = $versionStorage;
    }

    /**
     * Add all the migrations without running them.
     * This should be executed on new database installations.
     */
    public function initialize()
    {
        if ($this->versionStorage->hasVersioningNode()) {
            throw MigratorException::cannotInitializeAlreadyHasVersions();
        }

        foreach (array_keys($this->versionCollection->getAllVersions()) as $timestamp) {
            $this->versionStorage->add($timestamp);
        }

        $this->session->save();
    }

    public function migrate($to = null, OutputInterface $output)
    {
        if ($to === null) {
            $to = $this->versionCollection->getLatestVersion();
        } else {
            $to = 'V' . $to;
        }

        if (false === $to) {
            return array();
        }

        $to = (string) $to;

        if (!$this->versionCollection->has($to)) {
            throw MigratorException::unknownVersion($to);
        }

        $from = $this->versionStorage->getCurrentVersion();
        $direction = $from > $to ? 'down' : 'up';

        $versionsToExecute = $this->versionCollection->getVersions($from, $to, $direction);

        if (!$versionsToExecute) {
            return array();
        }

        $start = microtime(true);
        $position = 0;
        $output->writeln(sprintf('<comment>%s</comment> %d version(s):', ($direction == 'up' ? 'Upgrading' : 'Reverting'), count($versionsToExecute)));
        foreach ($versionsToExecute as $timestamp => $version) {
            $position++;
            $output->writeln(sprintf(' %s [<info>%d/%d</info>]: %s', $direction == 'up' ? '+' : '-', $position, count($versionsToExecute), $timestamp));
            $version->$direction($this->session);

            if ($direction === 'down') {
                $this->versionStorage->remove($timestamp);
            } else {
                $this->versionStorage->add($timestamp);
            }

            $this->session->save();
        }



        return $versionsToExecute;
    }
}
