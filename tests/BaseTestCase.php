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

use Jackalope\RepositoryFactoryFilesystem;
use PHPCR\SimpleCredentials;
use Symfony\Component\Filesystem\Filesystem;

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
