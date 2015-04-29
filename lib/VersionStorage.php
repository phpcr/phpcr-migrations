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

class VersionStorage
{
    private $session;
    private $storageNodeName;
    private $initialized = false;

    public function __construct(SessionInterface $session, $storageNodeName = 'phpcrmig:versions')
    {
        $this->session = $session;
        $this->storageNodeName = $storageNodeName;
    }

    public function init()
    {
        if ($this->initialized) {
            return;
        }

        $this->workspace = $this->session->getWorkspace();
        $nodeTypeManager = $this->workspace->getNodeTypeManager();

        if (!$nodeTypeManager->hasNodeType('phpcrmig:version')) {
            $nodeTypeManager->registerNodeTypesCnd(<<<EOT
<phpcrmig = 'http://www.danteech.com/phpcr-migrations'>
[phpcrmig:version] > nt:base, mix:created

[phpcrmig:versions] > nt:base
+* (phpcrmig:version)
EOT
            , true);
        }

        $rootNode = $this->session->getRootNode();

        if ($rootNode->hasNode($this->storageNodeName)) {
            $storageNode = $rootNode->getNode($this->storageNodeName);
        } else {
            $storageNode = $rootNode->addNode($this->storageNodeName, 'phpcrmig:versions');
        }

        $this->storageNode = $storageNode;
    }

    public function getPersistedVersions()
    {
        $this->init();

        $versionNodes = $this->storageNode->getNodes();
        $versions = array();

        foreach ($versionNodes as $versionNode) {
            $versions[$versionNode->getName()] = array(
                'name' => $versionNode->getName(),
                'executed' => $versionNode->getPropertyValue('jcr:created'),
            );
        }

        return $versions;
    }

    public function hasVersioningNode()
    {
        return $this->session->nodeExists('/' . $this->storageNodeName);
    }

    public function getCurrentVersion()
    {
        $this->init();

        $versions = (array) $this->storageNode->getNodeNames();

        if (!$versions) {
            return;
        }

        asort($versions);

        return end($versions);
    }

    public function remove($timestamp)
    {
        $this->init();

        $this->storageNode->getNode($timestamp)->remove();
    }

    public function add($timestamp)
    {
        $this->init();

        $node = $this->storageNode->addNode($timestamp, 'phpcrmig:version');
    }
}
