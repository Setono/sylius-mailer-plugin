<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->addPathToExclude(__DIR__ . '/tests')
    ->ignoreErrorsOnPackage('swiftmailer/swiftmailer', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/mime', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/twig-bridge', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreUnknownClasses([
        Symfony\Component\Mailer\Event\MessageEvent::class,
        Symfony\Component\Mailer\Event\SentMessageEvent::class,
        Symfony\Component\Mailer\MailerInterface::class,
    ])
;
