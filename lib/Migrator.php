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

use PHPCR\Migrations\Exception\MigratorException;
use PHPCR\SessionInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrator
{
    private $session;
    private $versionCollection;
    private $versionStorage;
    private $actions = [
        'up', 'down', 'top', 'bottom',
    ];

    public function __construct(
        SessionInterface $session,
        VersionCollection $versionCollection,
        VersionStorage $versionStorage
    ) {
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
            throw new MigratorException('This repository has already been initialized. Will not re-initialize.');
        }

        foreach (array_keys($this->versionCollection->getAllVersions()) as $timestamp) {
            $this->versionStorage->add($timestamp);
        }

        $this->session->save();
    }

    /**
     * Run the migration up (or down) until $to.
     *
     * If $to is 0 then all migrations will be reverted.
     * If $to is null then all migrations will be executed.
     *
     * @param string|null $to Version to run until
     *
     * @return VersionInterface[] Executed migrations
     */
    public function migrate($to, OutputInterface $output)
    {
        if (false === $to) {
            return [];
        }

        $from = $this->versionStorage->getCurrentVersion();
        $to = $this->resolveTo($to, $from);

        $direction = $from > $to ? 'down' : 'up';

        $versionsToExecute = $this->versionCollection->getVersions($from, $to, $direction);

        if (!$versionsToExecute) {
            return [];
        }

        $position = 0;
        $output->writeln(sprintf('<comment>%s</comment> %d version(s):', 'up' == $direction ? 'Upgrading' : 'Reverting', count($versionsToExecute)));
        foreach ($versionsToExecute as $timestamp => $version) {
            ++$position;
            $output->writeln(sprintf(' %s [<info>%d/%d</info>]: %s', 'up' == $direction ? '+' : '-', $position, count($versionsToExecute), $timestamp));
            $version->$direction($this->session);

            if ('down' === $direction) {
                $this->versionStorage->remove($timestamp);
            } else {
                $this->versionStorage->add($timestamp);
            }

            $this->session->save();
        }

        return $versionsToExecute;
    }

    public function getVersions()
    {
        return $this->versionCollection->getAllVersions();
    }

    /**
     * Resolve the "to" version.
     *
     * @param string $to
     * @param string $from
     *
     * @return string
     */
    private function resolveTo($to, $from)
    {
        if (is_string($to)) {
            $to = strtolower($to);
        }

        if ('down' === $to) {
            $to = $this->versionCollection->getPreviousVersion($from);
        }

        if ('up' === $to) {
            $to = $this->versionCollection->getNextVersion($from);
        }

        if ('bottom' === $to) {
            $to = 0;
        }

        if ('top' === $to || null === $to) {
            $to = $this->versionCollection->getLatestVersion();
        }

        if (0 !== $to && false === strtotime($to)) {
            throw new MigratorException(sprintf('Unknown migration action "%s". Known actions: "%s"', $to, implode('", "', $this->actions)));
        }

        if (0 !== $to && !$this->versionCollection->has($to)) {
            throw new MigratorException(sprintf('Unknown version "%s"', $to));
        }

        return $to;
    }
}
