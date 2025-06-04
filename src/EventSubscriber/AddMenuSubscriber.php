<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\EventSubscriber;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddMenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.menu.admin.main' => 'add',
        ];
    }

    public function add(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $menu
            ->addChild('setono_sylius_mailer')
            ->setLabel('setono_sylius_mailer.ui.mailer')
                ->addChild('setono_sylius_mailer_email_records', [
                    'route' => 'setono_sylius_mailer_admin_email_record_index',
                ])
                ->setLabel('setono_sylius_mailer.ui.email_log')
                ->setLabelAttribute('icon', 'truck')
        ;
    }
}
