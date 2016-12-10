<?php
// src/Sdz/BlogBundle/DependencyInjection/SdzBlogExtension.php

namespace Sdz\BlogBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SdzBlogExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        /**
         * La m�thode load() de cet objet est automatiquement ex�cut�e par Symfony lorsque le bundle est charg�. Et dans cette
         * m�thode, on charge le fichier de configuration services.yml, ce qui permet d'enregistrer la d�finition des services qu'il
         * contient dans le conteneur de services.
         */
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}