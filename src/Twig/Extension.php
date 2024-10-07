<?php

declare(strict_types=1);

namespace Setono\SyliusMailerPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class Extension extends AbstractExtension
{
    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('setono_sylius_mailer_iframe', $this->iframe(...), ['is_safe' => ['html']]),
        ];
    }

    public function iframe(?string $html, string $height = '50vh'): string
    {
        if (null === $html) {
            return '';
        }

        return '<iframe srcdoc="' . htmlspecialchars($html, \ENT_QUOTES, 'UTF-8') . '" style="width: 100%; height: ' . $height . '; border: none;"></iframe>';
    }
}
