<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusMailerExtension extends AbstractResourceExtension implements PrependExtensionInterface
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

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('sylius_grid', [
            'grids' => [
                'setono_sylius_mailer_admin_sent_email' => [
                    'driver' => [
                        'name' => 'doctrine/orm',
                        'options' => [
                            'class' => '%setono_sylius_mailer.model.sent_email.class%',
                        ],
                    ],
                    'sorting' => [
                        'createdAt' => 'desc',
                    ],
                    'limits' => [
                        100, 250, 500, 1000,
                    ],
                    'filters' => [
                        'search' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.search',
                            'options' => [
                                'fields' => [
                                    'subject',
                                    'to',
                                    'from',
                                ],
                            ],
                        ],
                        'createdAt' => [
                            'type' => 'date',
                            'label' => 'setono_sylius_mailer.ui.sent_at',
                            'options' => [
                                'field' => 'createdAt',
                                'inclusive_to' => true,
                            ],
                        ],
                    ],
                    'fields' => [
                        'subject' => [
                            'type' => 'string',
                            'label' => 'setono_sylius_mailer.ui.subject',
                        ],
                        'to' => [
                            'type' => 'twig',
                            'label' => 'setono_sylius_mailer.ui.to',
                            'options' => [
                                'template' => '@SetonoSyliusMailerPlugin/admin/grid/array_to_string.html.twig',
                            ],
                        ],
                        'from' => [
                            'type' => 'twig',
                            'label' => 'setono_sylius_mailer.ui.from',
                            'options' => [
                                'template' => '@SetonoSyliusMailerPlugin/admin/grid/array_to_string.html.twig',
                            ],
                        ],
                        'createdAt' => [
                            'type' => 'datetime',
                            'label' => 'setono_sylius_mailer.ui.sent_at',
                        ],
                    ],
                    'actions' => [
                        'item' => [
                            'show' => [
                                'type' => 'show',
                                'label' => 'sylius.ui.show',
                                'options' => [
                                    'link' => [
                                        'route' => 'setono_sylius_mailer_admin_sent_email_show',
                                        'parameters' => [
                                            'id' => 'resource.id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
