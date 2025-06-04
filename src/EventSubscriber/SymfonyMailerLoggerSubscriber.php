<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusMailerPlugin\Model\EmailRecordInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\SentMessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;

final class SymfonyMailerLoggerSubscriber implements EventSubscriberInterface
{
    use ORMTrait;

    /**
     * Emails indexed by object hash
     *
     * @var array<string, EmailRecordInterface>
     */
    private array $emailRecords = [];

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly FactoryInterface $emailFactory,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => ['log', 10], // is triggered _before_ \Symfony\Component\Mailer\EventListener\MessageListener::onMessage()
            SentMessageEvent::class => ['save', -10],
        ];
    }

    public function log(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message instanceof Email) {
            return;
        }

        /** @var EmailRecordInterface $emailRecord */
        $emailRecord = $this->emailFactory->createNew();

        $emailRecord->setSubject($message->getSubject());
        $emailRecord->setTextBody(self::convertBody($message->getTextBody()));
        $emailRecord->setHtmlBody(self::convertBody($message->getHtmlBody()));

        $emailRecord->setTo(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getTo())));
        $emailRecord->setFrom(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getFrom())));
        $emailRecord->setReplyTo(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getReplyTo())));
        $emailRecord->setCc(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getCc())));
        $emailRecord->setBcc(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getBcc())));

        if ($message instanceof TemplatedEmail) {
            $emailRecord->setTemplate($message->getHtmlTemplate());
        }

        // The SentMessageEvent class does not exist in Symfony 5.4
        if (class_exists(SentMessageEvent::class)) {
            $this->emailRecords[spl_object_hash($message)] = $emailRecord;
        } else {
            $manager = $this->getManager($emailRecord);
            $manager->persist($emailRecord);
            $manager->flush();
        }
    }

    public function save(SentMessageEvent $event): void
    {
        $message = $event->getMessage()->getOriginalMessage();
        if (!isset($this->emailRecords[spl_object_hash($message)])) {
            return;
        }

        $emailRecord = $this->emailRecords[spl_object_hash($message)];
        unset($this->emailRecords[spl_object_hash($message)]);

        $manager = $this->getManager($emailRecord);
        $manager->persist($emailRecord);
        $manager->flush();
    }

    /**
     * @param string|resource|null $body
     */
    private static function convertBody(mixed $body): ?string
    {
        if (null === $body) {
            return null;
        }

        if (is_resource($body)) {
            return (new TextPart($body))->getBody();
        }

        return $body;
    }
}
