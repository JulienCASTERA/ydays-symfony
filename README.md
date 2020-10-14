# Projet Ydays Symfony Intro

## Installer le projet


### Configurer Docker
````
cd docker
docker-compose build
docker-compose up -d
````

#### Installer les dépendances

````
# dans le dossier docker
docker-compose exec php bash
composer install
bin/console d:s:u --force
bin/console d:f:l
````

Let's go sur http://127.0.0.1:8080