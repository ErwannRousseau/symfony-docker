<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use NumberFormatter;

#[AsTwigComponent('product:price')]
final class Price
{
    public float $price;
    public string $locale = 'en_US';

    public function formatPrice(): string
    {
        $formatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($this->price, 'USD');
    }
}
