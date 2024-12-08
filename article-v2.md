# Pourquoi associer Tailwind CSS et Twig Components est une excellente combinaison pour le frontend d'un monolithe Symfony

Symfony, avec Twig pour le templating, reste un choix incontournable dans l’écosystème PHP, tandis que Tailwind CSS s’est imposé comme un des leader des framework CSS. Associés, ces outils apportent des synergies intéressantes.

## Pourquoi associer Tailwind CSS et Twig Components ?

Symfony est réputé comme un solide framework backend, mais son écosystème frontend est souvent perçu comme limité. Twig, le moteur de templates, fournit une syntaxe plus ou moins apprecié pour vos vues, tandis que Tailwind CSS permet un stylage rapide et cohérent grâce à ses classes utilitaires.

Les Twig Components, sont introduits pour encourager la réutilisabilité et la clarté dans les vues, et peuvent s’intègrer harmonieusement avec Tailwind CSS. Tailwind permettent de styliser rapidement les composants Twig, tandis que les Twig Components facilitent la réutilisation de ces composant.

## Présentation rapide des outils

Avant d’explorer leurs synergies, voici un rappel des fonctionnalités principales de chaque outil :

### Twig Components

Twig Components sont une évolution apporté dans Symfony UX, permettant de structurer vos templates en composants réutilisables.

- **Anonymous Components** : simples et sans logique, juste un template Twig.
- **Class Components** : pour les composants plus complexes, avec des méthodes et des propriétés. Une classe PHP associée à un template Twig.

Il existe aussi les **Live Components**, qui permettent de mettre à jour dynamiquement le DOM. Si vous voulez en savoir plus, je vous invite à consulter [Symfony UX Live Components Documentation](https://symfony.com/bundles/ux-live-component/current/index.html)

### Tailwind CSS

Tailwind CSS est un framework CSS utility-first. Contrairement aux frameworks CSS traditionnels (comme *Bootstrap*), il favorise l’utilisation de classes utilitaires qui permettent un stylage rapide et sans limites. Il est particulièrement apprécié pour sa flexibilité et son écosystème.

Totalement customisable grace à son fichier de configuration `tailwind.config.js`, Tailwind CSS permet de personnaliser les couleurs, les tailles, les polices, etc. Une bonne configuration est un atout pour une utilisation efficace de Tailwind.

## Synergies entre Twig Components et Tailwind CSS

### Anonymous Components

Prenons un exemple simple, un bouton avec des variantes de couleur et de taille.

Il existe un approche appelée **CVA** (*Class Variant Authority*), qui centralise la gestion des classes CSS conditionnelles, rendant vos composants modulaires, faciles à faire évoluer et à maintenir. Initialement popularisé dans le monde JavaScript, elle est désormait disponible dans Twig !

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

*Retrouvez un autre exemple d'utilisation pour les headings sur notre [repo de démonstration](https://github.com/KnpLabs/symfony-tailwind-twig-components/blob/main/templates/components/heading.html.twig)*

#### Bonus `tailwind_merge`

Notez ici l'utiliation du filtre `tailwind_merge` qui permet de fusionner les classes Tailwind en doublon, conflit quand vous surchargez les classes de vos composants.

```twig
<twig:button as="a" href="https://knplabs.com" size="lg" class="bg-red-500">Rouge KNPlabs</twig:button>
```

Rendu HTML:

```html
<a href="https://knplabs.com" class="inline-flex items-center justify-center gap-2 w-fit whitespace-nowrap rounded-md text-sm font-medium transition-colors h-12 px-8 bg-red-500">
    Rouge KNPlabs
</a>
```

Au lieu d'avoir des classes en conflit `bg-primary bg-red-500`, le filtre `tailwind_merge` fusionne les classes en doublon pour n'avoir que `bg-red-500` car on a surcharger la class de notre composant. Et on aura comme résultat un bouton rouge.

Filtre issue du bundle [tales-from-a-dev/twig-tailwind-extra: 🌱 A Twig extension for Tailwind](https://github.com/tales-from-a-dev/twig-tailwind-extra)

### Class Components

Vous pouvez aussi créer des composants avec des classes PHP, pour des cas plus complexes nécessitant une certaine logique.

Prenons un exemple simple de formatage d'un prix. Imaginons que votre contrôleur vous renvoie un prix, et que vous voulez l'afficher en euros. Par exemple, `12.34` devrait être affiché comme `12,34 €`.

Vous pouvez créer un Twig Component avec une classe PHP et un template pour gerer le rendu. Ce composant acceptera des propriétés `price` et `locale`, et expose une méthode `formatPrice`.

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

Dans cet exemple, l'attributes `#[AsTwigComponent('product:price')]` définit le nom du composant, qui sera automatiquement lié au fichier `templates/components/product/price.html.twig`. Voici à quoi pourrait ressembler le template Twig stylisé avec Tailwind :

```twig
<span {{ attributes.without('class') }}
    class="w-fit bg-gray-300 rounded-full px-3 py-1 text-sm font-semibold text-gray-900 {{ attributes.render('class')|tailwind_merge }}"
>
    {{ this.formatPrice }}
</span>
```

Dans ce template, la méthode `formatPrice` est appelée via `this.formatPrice`

#### Usage

```twig
<twig:product:price price="19.99" />
```

#### Rendu HTML

```html
<span class="w-fit px-3 py-1 bg-gray-300 rounded-full text-sm font-semibold text-gray-900">
    19,99 €
</span>
```

##### Bonus: Testez vos composants

Vous pouvez tester vos composants Twig avec PHPUnit avec une simplicité déconcertante. Voici un exemple de test pour notre composant `Price`.

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

        $this->assertStringContainsString('99,99 €', $rendered);
    }
}
```

Et voilà rien de plus compliqué que ca pour tester vos composants Twig. On test le montage du composant et on test le rendu du composant, tout simplement.

## Limites

1. **Lisibilité du code**

    L'abondance de classes Tailwind dans un même fichier peut rendre les templates volumineux difficiles à lire. Une bonne connaissances des utilities de Tailwind est nécessaire pour préserver une bonne lisibilité.

2. **Courbe d’apprentissage**

    Bien que Tailwind soit facile à utiliser, sa logique utility-first peut dérouter les nouveaux développeurs habitués aux approches basées sur CSS traditionnel.
    Tailwind reste relativement simple à apprendre et la documentation est très bien faite.

## Conclusion

Associer Twig Components et Tailwind CSS peut grandement moderniser votre workflow de développement frontend avec Symfony. Ces outils se complètent parfaitement : Twig apporte une structure modulaire, tandis que Tailwind accélère le stylage.

Cependant, leur utilisation nécessite une bonne organisation et une compréhension des limites pour tirer pleinement parti de leurs synergies. Cette combinaison convient particulièrement aux équipes qui cherchent une approche moderne et modulable un peu à la manière des composants React.

Pour les plus curieux, vous pouvez retrouver un exemple de cette stack sur notre [repo de demonstration](https://github.com/KnpLabs/symfony-tailwind-twig-components).

Et vous ? Avez-vous déjà essayé cette stack ? Partagez vos retours d’expérience ou posez vos questions dans les commentaires !
