<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusMailerPlugin\Model\EmailInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\SentMessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;

final class LogSymfonyEmailSubscriber implements EventSubscriberInterface
{
    use ORMTrait;

    /**
     * Emails indexed by object hash
     *
     * @var array<string, EmailInterface>
     */
    private array $emails = [];

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

        /** @var EmailInterface $email */
        $email = $this->emailFactory->createNew();

        $email->setSubject($message->getSubject());
        $email->setTextBody(self::convertBody($message->getTextBody()));
        $email->setHtmlBody(self::convertBody($message->getHtmlBody()));

        $from = $message->getFrom();
        if (count($from) > 0) {
            $email->setSenderAddress($from[0]->getAddress());

            $senderName = $from[0]->getName();
            $email->setSenderName('' === $senderName ? null : $senderName);
        }

        $email->setTo(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getTo())));
        $email->setReplyTo(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getReplyTo())));
        $email->setCc(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getCc())));
        $email->setBcc(array_values(array_map(static fn (Address $address) => $address->getAddress(), $message->getBcc())));

        if ($message instanceof TemplatedEmail) {
            $email->setTemplate($message->getHtmlTemplate());
        }

        $this->emails[spl_object_hash($message)] = $email;
    }

    public function save(SentMessageEvent $event): void
    {
        $message = $event->getMessage()->getOriginalMessage();
        if (!isset($this->emails[spl_object_hash($message)])) {
            return;
        }

        $email = $this->emails[spl_object_hash($message)];

        $manager = $this->getManager($email);
        $manager->persist($email);
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
