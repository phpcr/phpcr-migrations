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

use PHPCR\SessionInterface;

/**
 * Interface for PHPCR migration classes.
 *
 * Version classes MUST be named as follows:
 *
 *     VersionYYYYMMDDHHMM
 *
 * For example:
 *
 *     Version201504241609
 */
interface VersionInterface
{
    /**
     * Migrate the repository up.
     *
     * @param SessionInterface $session
     */
    public function up(SessionInterface $session);

    /**
     * Migrate the system down.
     *
     * @param SessionInterface $session
     */
    public function down(SessionInterface $session);
}
