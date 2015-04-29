<?php

namespace DTL\PhpcrMigrations;

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
