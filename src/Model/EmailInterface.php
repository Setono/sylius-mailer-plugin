<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface EmailInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getSubject(): ?string;

    public function setSubject(?string $subject): void;

    public function getTextBody(): ?string;

    public function setTextBody(?string $textBody): void;

    public function getHtmlBody(): ?string;

    public function setHtmlBody(?string $htmlBody): void;

    public function getSenderName(): ?string;

    public function setSenderName(?string $senderName): void;

    public function getSenderAddress(): ?string;

    public function setSenderAddress(?string $senderAddress): void;

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
