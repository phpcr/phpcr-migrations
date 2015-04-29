<?php

use DTL\PhpcrMigrations\VersionInterface;
use PHPCR\SessionInterface;

class Version201501011212 implements VersionInterface
{
    public function up(SessionInterface $session)
    {
        $session->getNode('/hello')->addNode('world');
    }

    public function down(SessionInterface $session)
    {
        $session->getNode('/hello')->getNode('world')->remove();
    }
}
