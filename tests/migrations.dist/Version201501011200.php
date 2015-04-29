<?php

use DTL\PhpcrMigrations\VersionInterface;
use PHPCR\SessionInterface;

class Version201501011200 implements VersionInterface
{
    public function up(SessionInterface $session)
    {
        $session->getRootNode()->addNode('hello');
    }

    public function down(SessionInterface $session)
    {
        $session->getRootNode()->getNode('hello')->remove();
    }
}
