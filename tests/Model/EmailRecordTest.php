<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\Tests\Model;

use PHPUnit\Framework\TestCase;
use Setono\SyliusMailerPlugin\Model\EmailRecord;

final class EmailRecordTest extends TestCase
{
    /**
     * @test
     */
    public function it_trims_subject(): void
    {
        $emailRecord = new EmailRecord();
        $emailRecord->setSubject(' subject ');

        self::assertSame('subject', $emailRecord->getSubject());
    }
}
