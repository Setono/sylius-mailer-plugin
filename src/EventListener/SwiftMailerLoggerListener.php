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
        $sentEmail->setFrom(self::convertAddresses($message->getFrom()));
        $sentEmail->setTo(self::convertAddresses($message->getTo()));
        $sentEmail->setReplyTo(self::convertAddresses($message->getReplyTo()));
        $sentEmail->setCc(self::convertAddresses($message->getCc()));
        $sentEmail->setBcc(self::convertAddresses($message->getBcc()));

        $manager = $this->getManager($sentEmail);
        $manager->persist($sentEmail);
        $manager->flush();
    }

    /**
     * @return list<string>|null
     */
    private static function convertAddresses(mixed $addresses): ?array
    {
        if (!is_array($addresses)) {
            return null;
        }

        /** @var list<string> $addresses */
        $addresses = array_keys($addresses);

        return $addresses;
    }
}
