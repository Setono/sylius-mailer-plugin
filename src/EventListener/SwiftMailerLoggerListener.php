<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusMailerPlugin\Model\SentEmailInterface;
use Swift_Events_SendEvent;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class SwiftMailerLoggerListener implements \Swift_Events_SendListener
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly FactoryInterface $emailFactory,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function beforeSendPerformed(Swift_Events_SendEvent $evt): void
    {
        // no op
    }

    public function sendPerformed(Swift_Events_SendEvent $evt): void
    {
        $message = $evt->getMessage();

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
