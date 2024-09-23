<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\DependencyInjection\Compiler;

use Setono\SyliusMailerPlugin\EventListener\SwiftMailerLoggerListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class RegisterSwiftMailerListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!ContainerBuilder::willBeAvailable('swiftmailer/swiftmailer', \Swift_Mailer::class, ['symfony/swiftmailer-bundle'])) {
            return;
        }

        if (!$container->hasParameter('swiftmailer.mailers')) {
            return;
        }

        $mailers = $container->getParameter('swiftmailer.mailers');
        Assert::isArray($mailers);

        foreach (array_keys($mailers) as $name) {
            try {
                $definition = $container->findDefinition(sprintf('swiftmailer.mailer.%s', $name));
            } catch (ServiceNotFoundException) {
                continue;
            }

            $definition->addMethodCall('registerPlugin', [new Reference(SwiftMailerLoggerListener::class)]);
        }
    }
}
