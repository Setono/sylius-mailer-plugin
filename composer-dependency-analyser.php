<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

return (new Configuration())
    ->addPathToExclude(__DIR__ . '/tests')
    ->ignoreUnknownClasses([
        Symfony\Component\Mailer\Event\SentMessageEvent::class, // This does not exist in SF5.4
    ])
    ->disableReportingUnmatchedIgnores()
;
