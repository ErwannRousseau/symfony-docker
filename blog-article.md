# Modernisez vos applications Symfony avec Tailwind et Twig Components

Symfony est depuis longtemps un incontournable pour développer des applications web PHP. Mais qu’en est-il du frontend ? Grâce à Tailwind CSS et aux Twig Components, vous pouvez transformer vos interfaces en un rien de temps tout en restant organisé et maintenable. Dans cet article, découvrez comment combiner ces trois outils pour créer des applications Symfony à la fois modernes et élégantes.

## Prérequis

Une application Symfony >= 6.3 et Twig >= 3.0. Voir la documentation officielle : [Installing & Setting up the Symfony Framework (Symfony Docs)](https://symfony.com/doc/current/setup.html).

Dans cet article, nous utiliserons une app Symfony 7.1 avec Symfony AssetMapper. Pas besoin d'installer Webpack Encore (bien que totalement compatible), ce qui simplifie l'installation.

## Installation

```bash
composer require symfony/asset-mapper symfony/twig-bundle symfony/ux-twig-component
```

Ceci installe les dépendances nécessaires pour utiliser les Twig Components.

Ensuite, il faut installer Tailwind CSS :

```bash
composer require symfonycasts/tailwind-bundle tales-from-a-dev/twig-tailwind-extra
php bin/console tailwind:init
```

Le fichier `tailwind.config.js` sera créé à la racine de votre projet. Une bonne configuration de Tailwind est essentielle pour une utilisation optimale.

### Conseil sur les conventions de nommage

Dans notre équipe, lors d'une refonte graphique avec des maquettes Figma, nous n’avions pas défini de convention claire pour les variables de style. Résultat : des classes en `Camel-Case` (`bg-Grey-Light`), peu adaptées à Tailwind. Après discussion, nous avons opté pour une convention en `kebab-case` (`bg-grey-light`), respectant les conventions Tailwind et facilitant la maintenance.

## Utilisation

Incluez le CSS de Tailwind dans le template de base :

```twig
{# templates/base.html.twig #}
{% block stylesheets %}
    <link rel="stylesheet" href="{{ asset('styles/app.css') }}">
{% endblock %}
```

En développement, utilisez :

```bash
php bin/console tailwind:build --watch
```

Plus d'infos dans la [documentation officielle du bundle Tailwind.](https://symfony.com/bundles/TailwindBundle/current/index.html)

```twig
{# templates/default.html.twig #}
{% extends 'base.html.twig' %}
{% block body %}
    <div class="container">
        <h1 class="text-3xl font-bold">Accueil</h1>
        <p class="text-gray-500">Bienvenue chez KNPlabs !</p>
    </div>
{% endblock %}
```

## Configuration avancée

Tailwind est extrêmement flexible. Vous pouvez personnaliser les couleurs, polices, tailles, etc., dans le fichier `tailwind.config.js`. Par exemple, définissez la classe `container` pour centrer le contenu et ajouter du padding différents selon les tailles d’écran :

```js
theme: {
  container: {
    center: true,
    padding: {
      DEFAULT: '1rem',
      lg: '2rem',
    },
  },
},
```

Ajoutez des couleurs personnalisées, raduis, etc. dans la clé `extend` :

```js
extend: {
  colors: {
    background: "#ffffff",
    foreground: "#020817",
    primary: {
      DEFAULT: "#0f172a",
      foreground: "#f8fafc",
    },
    secondary: {
      DEFAULT: "#f1f5f9",
      foreground: "#0f172a",
    },
  },
  borderRadius: {
    lg: "0.6rem",
    md: "0.4rem",
  },
},
```

Vous pouvez retrouver une configuration plus complète dans notre [repo de démonstration](https://github.com/KnpLabs/symfony-tailwind-twig-components/blob/main/tailwind.config.js).

## Twig Components

Twig Components, un composant Symfony, permet de créer des composants réutilisables dans vos templates Twig. Ces composants peuvent être :

- Anonymes (Anonymous Twig Component) : fichiers Twig simples dédiés à l’UI.
- Avec logique métier (Live Twig Component) : classes PHP pour des composants plus complexes, avec un template Twig associé.

### Anonymous Twig Component et approche CVA

Avec Twig Components, vous pouvez gérer les variantes de style CSS grâce à **CVA** (*Class Variant Authority*).

Le CVA centralise la gestion des classes CSS conditionnelles, rendant vos composants modulaires et faciles à maintenir. Initialement popularisé dans le monde JavaScript, il est désormais disponible dans Twig !

Voici un exemple de création d’un bouton avec variantes :

```twig
{# templates/components/button.html.twig #}

{% props as = 'button', variant = 'default', size = 'default' %}

{% set buttonVariants = cva({
    base: 'inline-flex items-center justify-center gap-2 w-fit whitespace-nowrap rounded-md text-sm font-medium transition-colors',
    variants: {
        variant: {
            default: 'bg-primary text-primary-foreground hover:bg-primary/90',
            secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        },
        size: {
            default: 'h-10 px-4 py-2',
            lg: 'h-12 rounded-md px-8',
        }
    },
    defaultVariants: {
        variant: 'default',
        size: 'default',
    }
}) %}

<{{ as }} {{ attributes.without('class') }}
    class="{{ buttonVariants.apply({variant, size}, attributes.render('class'))|tailwind_merge }}"
>
    {% block content '' %}
</{{ as }}>
```

Voici notre composant bouton qui accepte trois props : `as`, `variant`, et `size`. Cette approche de composant dite "polymorphic" permet de rendre par défaut le composant comme une balise `button`, mais vous pouvez le changer en `a`, `div`, ou autre en passant la prop `as`.

#### Usage

```twig
<twig:button as="a" href="https://knplabs.com" size="lg">Découvrir KNPlabs</twig:button>
```

#### Rendu HTML

```html
<a href="https://knplabs.com" class="inline-flex items-center justify-center gap-2 w-fit whitespace-nowrap rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-12 px-8">
    Découvrir KNPlabs
</a>
```

*Retrouvez un autre cas d'utilisation pour les headings sur notre [repo de démonstration](https://github.com/KnpLabs/symfony-tailwind-twig-components/blob/main/templates/components/heading.html.twig)*

##### Bonus: Tailwind Merge

Le filtre `tailwind_merge` permet de fusionner les classes CSS de manière intelligente. Par exemple, si vous surchager votre composant avec d'autre classes Tailwind en doublon ou en conflict, `tailwind_merge` les fusionnera et appliquera le style surchager en dernier. Cela évite l'utilisation pas très propre du `!` devant vos classes Tailwind.

```twig
<twig:button as="a" href="https://knplabs.com" size="lg" class="bg-red-500">Rouge KNPlabs</twig:button>
```

Rendu HTML:

```html
<a href="https://knplabs.com" class="inline-flex items-center justify-center gap-2 w-fit whitespace-nowrap rounded-md text-sm font-medium transition-colors h-12 px-8 bg-red-500">
    Rouge KNPlabs
</a>
```

### Live Twig Components

Comme je le disais, vous pouvez aussi créer des composants avec des classes PHP, pour des cas plus complexes nécessitant une certaine logique métier.

Prenons un exemple très simple de formatage d'un prix. Imaginons que votre contrôleur vous renvoie un prix, et que vous voulez l'afficher en euros. Par exemple, `12.34` devrait être affiché comme `12,34 €`.

Vous pouvez créer un Live Twig Component. Ce composant acceptera des propriétés `price` et `locale`, et expose une méthode `formatPrice`.

```php
<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use NumberFormatter;

#[AsTwigComponent('product:price')]
final class Price
{
    public float $price;
    public string $locale = 'fr_FR';

    public function formatPrice(): string
    {
        $formatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($this->price, 'EUR');
    }
}
```

Dans cet exemple, le attributes `#[AsTwigComponent('product:price')]` définit le nom du composant, qui sera automatiquement lié au fichier `templates/components/product/price.html.twig`. Voici à quoi pourrait ressembler le template Twig :

```twig
<span {{ attributes.without('class') }}
    class="w-fit bg-gray-300 rounded-full px-3 py-1 text-sm font-semibold text-gray-900 {{ attributes.render('class')|tailwind_merge }}"
>
    {{ this.formatPrice }}
</span>
```

Dans ce template, la méthode `formatPrice` est appelée via `this.formatPrice`, permettant ainsi d'afficher le prix formaté de manière propre et réutilisable dans votre application.

#### Pourquoi utiliser cette approche ?

Avec cette approche, vous pouvez :

- **Séparer la logique métier du rendu** : La méthode `formatPrice` encapsule la logique de formatage.
- **Tirer parti de la puissance de PHP** : Vous avez accès à toutes les fonctionnalités de PHP pour construire vos composants, comme ici avec `NumberFormatter`.

Fini les Twig Extension qui retournent du HTML. Grâce à cette méthode, vous obtenez des composants puissants, clairs et réutilisables.

#### Exemple d'utilisation

Voici comment intégrer ce composant dans un template Twig :

```twig
<twig:product:price price="19.99" />
```

Rendu HTML :

```html
<span class="w-fit bg-gray-300 rounded-full px-3 py-1 text-sm font-semibold text-gray-900">
    19,99 €
</span>
```

##### Bonus: Testez vos composants

Vous pouvez tester vos composants Twig avec PHPUnit d'une simplicité deconcertente. Voici un exemple de test pour notre composant `Price`.

```php
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
            data: ['price' => 99.99, 'locale' => 'fr_FR'],
        );

        $this->assertInstanceOf(Price::class, $component);
        $this->assertSame(99.99, $component->price);
        $this->assertSame('fr_FR', $component->locale);
    }

    public function testComponentRenders(): void
    {
        $rendered = $this->renderTwigComponent(
            name: Price::class,
            data: ['price' => 99.99, 'locale' => 'fr_FR'],
        );

        $this->assertStringContainsString('99,99 €', $rendered); // Notez l'espace insécable entre le montant et le symbole de l'euro
    }
}
```

Et voilà rien de plus compliqué que ca pour tester nos composants Twig. On test le mount du composant et on test le rendu du composant, tout simplement.

## Limites

- **Lisibilité du Code** : Twig peut parfois rendre le code plus verbeux, surtout avec des composants complexes ou lorsque plusieurs niveaux de composants sont imbriqués. La syntaxe de Twig n'est pas toujours la plus concise et peut rendre le DOM final difficile à appréhender, surtout pour les développeurs non familiers avec cette approche. Par exemple, l'utilisation de directives Twig à l’intérieur des attributs HTML pour manipuler des classes, des styles, etc., peut rapidement rendre le code difficile à lire.

- **Séparation de la Logique et de la Présentation** : Lorsqu'on utilises à la fois des Twig Components et des classes PHP pour définir la logique, cela peut entraîner une certaine confusion. Par exemple, un composant Twig peut appeler une méthode définie par sa classe PHP, ce qui peut rendre la traçabilité du flux logique un peu floue, surtout pour des développeurs qui ne sont pas familiers avec ce type d'architecture.

- **Utilisation de Twig Extension vs Twig Components** : Twig permet créer des extensions ce qui peut simplifier certaines tâches. Additionné avec des Twig Components, Anonymous ou/et Live, cela peut créer un soucis d'harmonisation dans votre codebase.

## Conclusion

L’utilisation de Twig Components avec Tailwind CSS est une approche puissante et moderne pour développer le frontend d'un monolithe Symfony. Certes, cette méthodologie a ses limites, comme la lisibilité parfois complexe du DOM, dû aux parfoit nombreuses classes Tailwind et à Twig qui peut parfois rendre le code plus verbeux, ou bien la nécessité de trouver un équilibre entre logique PHP et Twig. Cependant, ces défis peuvent être surmontés avec une bonne organisation, un découpage rigoureux des composants et des conventions claires.

Selon notre d'expérience, nous sommes pleinement satisfaits de cette approche. Elle nous a permis d'intégrer nos maquettes de manière de plus en plus rapide et efficace grâce à une bibliothèque de composants que nous avons enrichie au fil du projet. Cette productivité accrue n’aurait pas été possible sans Tailwind CSS, qui simplifie et accélère grandement le stylage de nos composants et du reste de notre interface. En somme, cette combinaison s'est révélée être un excellent choix pour nos besoins.
