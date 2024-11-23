<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'index_products')]
    #[Template('products/index.html.twig')]
    public function index(): array
    {
        return [
            'products' => [
                [
                    'title' => 'Smartphone',
                    'description' => 'Latest model smartphone with advanced features',
                    'price' => 699.99,
                    'imageUrl' => 'https://unsplash.it/640/425?random',
                    'linkUrl' => '#'
                ],
                [
                    'title' => 'Laptop',
                    'description' => 'High performance laptop for professionals',
                    'price' => 1299.99,
                    'imageUrl' => 'https://unsplash.it/640/425?random',
                    'linkUrl' => '#'
                ],
                [
                    'title' => 'Smartwatch',
                    'description' => 'Stylish smartwatch with health tracking features',
                    'price' => 199.99,
                    'imageUrl' => 'https://unsplash.it/640/425?random',
                    'linkUrl' => '#'
                ],
                [
                    'title' => 'Headphones',
                    'description' => 'Noise-cancelling over-ear headphones',
                    'price' => 299.99,
                    'imageUrl' => 'https://unsplash.it/640/425?random',
                    'linkUrl' => '#'
                ],
            ],
        ];
    }
}