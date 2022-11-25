# ToDo-Co-OC-P8

## Build with 

-   Symfony 6.1
-   Twig
-   PhpUnit

## Requirements 

-   PHP 8
-   Composer
-   Web server
-   MYSQL

## Installation

-   Cloner / Télécharger le projet
-   Configurer votre serveur web pour qu'il pointe sur le répertoire du projet
-   Composer install
-   Copier le fichier .env et le renommer en .env.local  
-   Modifiez le fichier .env pour le connecter à votre serveur de base de données.
-   Exécutez la commande pour créer la base de données :  `php bin/console doctrine:database:create`
-   Lancer les migrations : `php bin/console doctrine:migrations:migrate`
-   Exécutez la commande pour créer lancer les fixtures :  `doctrine:fixtures:load`

## Test - PhpUnit

-   Edit your .env file with test data
-   Create the test database :  `php bin/console doctrine:database:create`
-   Load Fixtures : `php bin/console doctrine:fixtures:load --env.test`
-   Run test : `vendor/bin/phpunit --coverage-html public/code-coverage`

## Contribution

-   Installation du projet
-   Ecriture de la/les features ou corrections + test unitaire/fonctionnel correspondant
-   Relancer les test, vérifier code coverage et commit
