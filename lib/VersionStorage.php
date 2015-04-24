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

class VersionStorage
{
    private $session;
    private $storageNodeName;

    public function __construct(SessionInterface $session, $storageNodeName = 'phpcr:migrations')
    {
        $this->session = $session;
        $this->storageNodeName = $storageNodeName;
    }

    public function init()
    {
        $this->workspace = $this->session->getWorkspace();
        $nodeTypeManager = $this->workspace->getNodeTypeManager();

        if (!$nodeTypeManager->hasNodeType('phpcr:migrationversion')) {
            $nodeTypeManager->registerNodeTypesCnd(<<<EOT
<phpcrMigrations = 'http://www.danteech.com/phpcr-migrations'>
[phpcrMigrations:version] > nt:base
[phpcrMigrations:versions] > nt:base
+phpcrMigrations:version
EOT
            , true);
        }

        $rootNode = $this->session->getRootNode();

        if ($rootNode->hasNode($this->storageNodeName)) {
            $storageNode = $rootNode->getNode($this->storageNodeName);
        } else {
            $storageNode = $rootNode->addNode($this->storageNodeName, 'phpcrmigrations:migrations');
        }

        $this->storageNode = $storageNode;
    }

    public function getPersistedVersions()
    {
        $versions = $this->storageNode->getNodeNames();
        return $versions;
    }

    public function getCurrentVersion()
    {
        $versions = $this->storageNode->getNodeNames();
        asort($versions);
        return end($versions);
    }

    public function remove($timestamp)
    {
        $this->storageNode->getNode($timestamp)->remove();
    }

    public function add($timestamp)
    {
        $this->storageNode->addNode($timestamp, 'phpcrmigrations:version');
    }
}
