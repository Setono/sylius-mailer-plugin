<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\Tests\Model;

use PHPUnit\Framework\TestCase;
use Setono\SyliusMailerPlugin\Model\SentEmail;

final class SentEmailTest extends TestCase
{
    /**
     * @test
     */
    public function it_trims_subject(): void
    {
        $sentEmail = new SentEmail();
        $sentEmail->setSubject(' subject ');

        self::assertSame('subject', $sentEmail->getSubject());
    }
}
