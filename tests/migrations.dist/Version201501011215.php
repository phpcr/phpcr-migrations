<?php

use DTL\PhpcrMigrations\VersionInterface;
use PHPCR\SessionInterface;

class Version201501011215 implements VersionInterface
{
    public function up(SessionInterface $session)
    {
        $session->getNode('/hello')->addNode('dan');
    }

    public function down(SessionInterface $session)
    {
        $session->getNode('/hello')->getNode('dan')->remove();
    }
}
