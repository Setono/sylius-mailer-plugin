<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface SentEmailInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getSubject(): ?string;

    public function setSubject(?string $subject): void;

    public function getTextBody(): ?string;

    public function setTextBody(?string $textBody): void;

    public function getHtmlBody(): ?string;

    public function setHtmlBody(?string $htmlBody): void;

    public function getFrom(): array;

    /**
     * @param list<string>|null $from
     */
    public function setFrom(?array $from): void;

    public function getTo(): array;

    /**
     * @param list<string>|null $to
     */
    public function setTo(?array $to): void;

    public function getReplyTo(): array;

    /**
     * @param list<string>|null $replyTo
     */
    public function setReplyTo(?array $replyTo): void;

    public function getCc(): array;

    /**
     * @param list<string>|null $cc
     */
    public function setCc(?array $cc): void;

    public function getBcc(): array;

    /**
     * @param list<string>|null $bcc
     */
    public function setBcc(?array $bcc): void;

    public function getTemplate(): ?string;

    public function setTemplate(?string $template): void;
}
