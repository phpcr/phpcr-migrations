<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sulu\Bundle\ContentBundle;

use PHPCR\Migrations\VersionInterface;
use PHPCR\NodeInterface;
use PHPCR\SessionInterface;
use Bala\Bundle\ContentBundle\Blog\BasePageBlog;
use Bala\Bundle\BlogManagerBundle\Bridge\BlogInspector;
use Bala\Bundle\BlogManagerBundle\Bridge\PropertyEncoder;
use Bala\Component\Content\Metadata\BlockMetadata;
use Bala\Component\Content\Metadata\Factory\StructureMetadataFactoryInterface;
use Bala\Component\Content\Metadata\PropertyMetadata;
use Bala\Component\Content\Metadata\StructureMetadata;
use Bala\Component\BlogManager\BlogManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
 *
 * Created: 2015-12-10 10:04
 */
class Version201511240843 implements VersionInterface, ContainerAwareInterface
{
}
