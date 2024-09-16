<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusMailerPlugin\Model\SentEmailInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SwiftMailerLoggerSubscriber implements EventSubscriberInterface
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly FactoryInterface $emailFactory,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sendPerformed' => 'log',
        ];
    }

    public function log(\Swift_Events_SendEvent $event): void
    {
        $message = $event->getMessage();

        /** @var SentEmailInterface $sentEmail */
        $sentEmail = $this->emailFactory->createNew();

        $sentEmail->setSubject($message->getSubject());
        $sentEmail->setHtmlBody($message->getBody());

        /** @psalm-suppress MixedArgumentTypeCoercion */
        $sentEmail->setTo(array_keys($message->getTo()));

        /** @psalm-suppress MixedArgumentTypeCoercion */
        $sentEmail->setReplyTo(array_keys($message->getReplyTo()));

        /** @psalm-suppress MixedArgumentTypeCoercion */
        $sentEmail->setCc(array_keys($message->getCc()));

        /** @psalm-suppress MixedArgumentTypeCoercion */
        $sentEmail->setBcc(array_keys($message->getBcc()));

        $manager = $this->getManager($sentEmail);
        $manager->persist($sentEmail);
        $manager->flush();
    }
}
