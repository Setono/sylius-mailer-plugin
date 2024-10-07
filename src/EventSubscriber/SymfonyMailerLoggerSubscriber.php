<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusMailerPlugin\Model\SentEmailInterface;
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
     * @var array<string, SentEmailInterface>
     */
    private array $sentEmails = [];

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

        /** @var SentEmailInterface $sentEmail */
        $sentEmail = $this->emailFactory->createNew();

        $sentEmail->setSubject($message->getSubject());
        $sentEmail->setTextBody(self::convertBody($message->getTextBody()));
        $sentEmail->setHtmlBody(self::convertBody($message->getHtmlBody()));

        $sentEmail->setTo(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getTo())));
        $sentEmail->setFrom(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getFrom())));
        $sentEmail->setReplyTo(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getReplyTo())));
        $sentEmail->setCc(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getCc())));
        $sentEmail->setBcc(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getBcc())));

        if ($message instanceof TemplatedEmail) {
            $sentEmail->setTemplate($message->getHtmlTemplate());
        }

        $this->sentEmails[spl_object_hash($message)] = $sentEmail;
    }

    public function save(SentMessageEvent $event): void
    {
        $message = $event->getMessage()->getOriginalMessage();
        if (!isset($this->sentEmails[spl_object_hash($message)])) {
            return;
        }

        $sentEmail = $this->sentEmails[spl_object_hash($message)];

        $manager = $this->getManager($sentEmail);
        $manager->persist($sentEmail);
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
