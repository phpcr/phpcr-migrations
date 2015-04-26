<?php

namespace DTL\PhpcrMigrations;

use Jackalope\RepositoryFactoryFilesystem;
use Symfony\Component\Filesystem\Filesystem;
use PHPCR\SimpleCredentials;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected $session;

    public function initPhpcr()
    {
        $path = __DIR__ . '/data';
        $sfFs = new Filesystem();
        $sfFs->remove($path);
        $factory = new RepositoryFactoryFilesystem();
        $repository = $factory->getRepository(array(
            'path' => $path,
            'search.enabled' => false,
        ));
        $credentials = new SimpleCredentials('admin', 'admin');
        $this->session = $repository->login($credentials);
    }
}
