# Symfony avec Tailwind et Twig Components

Symfony est un framework PHP qui permet de créer des applications web rapidement. Tailwind est un framework CSS qui permet de créer des interfaces rapidement. Twig est le moteur de template utilisé dans une app Symfony. Dans cet article, nous allons voir comment combiner ces trois technologies, pour moderniser le frontend de nos applications Symfony.

## Prérequis

Une application Symfony >= 6.3 et Twig >= 3.0. Voir documentation officielle [Installing & Setting up the Symfony Framework (Symfony Docs)](https://symfony.com/doc/current/setup.html)

Dans cette article on se basera sur un app Symfony 7.1 avec le Symfony AssetMapper. Donc pas besoin d'installer Webpack Encore, bien que totalement compatible avec cette configuration, juste pour un soucis de simplicité.

## Installation

```bash
composer require symfony/asset-mapper symfony/twig-bundle symfony/ux-twig-component
```

Ceci va installer les dépendances nécessaires pour utiliser les composants Twig.

Ensuite il nous faut installer Tailwind CSS.

```bash
composer require symfonycasts/tailwind-bundle tales-from-a-dev/twig-tailwind-extra
```

Ceci va installer les dépendances nécessaires pour utiliser Tailwind CSS, ainsi qu'un petit utilitaire pour gerer les classes Tailwind dans vos templates Twig, on y reviendra plus tard.

```bash
php bin/console tailwind:init
```

Ceci va créer un fichier `tailwind.config.js` à la racine de votre app. Une bonne configuartion de Tailwind est essentielle pour une bonne utilisation de ce framework et gagner en productivité.

Si dans votre équipe vous avez un designer, je vous recommande de vous mettre d'accord sur une convention de nommage des variables en `kebab-case`, pour que les noms des classes Tailwind respectent les conventions du framework lui-même.

Je vous recommande ca, car dans notre équipe pour le refonte graphique du projet, nous avions la chance d'avoir un designer et des supers maquettes Figma, mais nous ne nous etions pas consulter avant de la nomanclature des variables utilisées dans les maquettes. Ce qui nous a valu de devoir nommer les classes en `"Camel-Case"` (ex: `Grey-Light`, ce qui donne `bg-Grey-Light` ) ce qui ne respecte pas vraimennt les conventions de Tailwind. Donc apres avoir discuter ensemble, nous avons convenu qu'il serait plus adapter de nommer les variables en `kebab-case` (ex: `grey-light`, ce qui donne `bg-grey-light`).

## Utilisation

En developpement, vous pouvez utiliser la commande suivante pour compiler votre CSS Tailwind.

```bash
bin/console tailwind:build --watch
```
