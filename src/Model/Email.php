<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class Email implements EmailInterface
{
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?string $subject = null;

    protected ?string $textBody = null;

    protected ?string $htmlBody = null;

    protected ?string $senderName = null;

    protected ?string $senderAddress = null;

    protected ?array $to = null;

    protected ?array $replyTo = null;

    protected ?array $cc = null;

    protected ?array $bcc = null;

    protected ?string $template = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getTextBody(): ?string
    {
        return $this->textBody;
    }

    public function setTextBody(?string $textBody): void
    {
        $this->textBody = $textBody;
    }

    public function getHtmlBody(): ?string
    {
        return $this->htmlBody;
    }

    public function setHtmlBody(?string $htmlBody): void
    {
        $this->htmlBody = $htmlBody;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    public function getSenderAddress(): ?string
    {
        return $this->senderAddress;
    }

    public function setSenderAddress(?string $senderAddress): void
    {
        $this->senderAddress = $senderAddress;
    }

    public function getTo(): array
    {
        return $this->to ?? [];
    }

    public function setTo(?array $to): void
    {
        if ([] === $to) {
            $to = null;
        }

        $this->to = $to;
    }

    public function getReplyTo(): array
    {
        return $this->replyTo ?? [];
    }

    public function setReplyTo(?array $replyTo): void
    {
        if ([] === $replyTo) {
            $replyTo = null;
        }

        $this->replyTo = $replyTo;
    }

    public function getCc(): array
    {
        return $this->cc ?? [];
    }

    public function setCc(?array $cc): void
    {
        if ([] === $cc) {
            $cc = null;
        }

        $this->cc = $cc;
    }

    public function getBcc(): array
    {
        return $this->bcc ?? [];
    }

    public function setBcc(?array $bcc): void
    {
        if ([] === $bcc) {
            $bcc = null;
        }

        $this->bcc = $bcc;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): void
    {
        $this->template = $template;
    }
}
