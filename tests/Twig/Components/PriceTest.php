<?php

namespace App\Tests\Twig\Components;

use App\Twig\Components\Price;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

class PriceTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testComponentMount(): void
    {
        $component = $this->mountTwigComponent(
            name: Price::class,
            data: ['price' => 99.99, 'locale' => 'en_US'],
        );

        $this->assertInstanceOf(Price::class, $component);
        $this->assertSame(99.99, $component->price);
        $this->assertSame('en_US', $component->locale);
    }

    public function testComponentRenders(): void
    {
        $rendered = $this->renderTwigComponent(
            name: Price::class,
            data: ['price' => 99.99, 'locale' => 'en_US'],
        );

        $this->assertStringContainsString('$99.99', $rendered);
    }
}
