<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusMailerExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{resources: array} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources(
            'setono_sylius_mailer',
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            $config['resources'],
            $container,
        );

        $loader->load('services.xml');

        if (ContainerBuilder::willBeAvailable('symfony/mailer', 'Symfony\Component\Mailer\MailerInterface', ['symfony/framework-bundle'])) {
            $loader->load('services/conditional/symfony_mailer.xml');
        }

        if (ContainerBuilder::willBeAvailable('swiftmailer/swiftmailer', '\Swift_Mailer', ['symfony/swiftmailer-bundle'])) {
            $loader->load('services/conditional/swiftmailer.xml');
        }
    }
}
