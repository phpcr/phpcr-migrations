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

use PHPCR\NodeInterface;
use PHPCR\SessionInterface;

class VersionStorage
{
    private $session;
    private $storageNodeName;
    private $initialized = false;
    /**
     * @var NodeInterface
     */
    private $storageNode;

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

        $workspace = $this->session->getWorkspace();
        $nodeTypeManager = $workspace->getNodeTypeManager();

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

        $this->storageNode = ($rootNode->hasNode($this->storageNodeName))
            ? $rootNode->getNode($this->storageNodeName)
            : $rootNode->addNode($this->storageNodeName, 'phpcrmig:versions')
        ;
    }

    public function getPersistedVersions()
    {
        $this->init();

        $versionNodes = $this->storageNode->getNodes();
        $versions = [];

        foreach ($versionNodes as $versionNode) {
            $versions[$versionNode->getName()] = [
                'name' => $versionNode->getName(),
                'executed' => $versionNode->getPropertyValue('jcr:created'),
            ];
        }

        return $versions;
    }

    public function hasVersioningNode()
    {
        return $this->session->nodeExists('/'.$this->storageNodeName);
    }

    public function getCurrentVersion()
    {
        $this->init();

        $versions = (array) $this->storageNode->getNodeNames();

        if (!$versions) {
            return null;
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
