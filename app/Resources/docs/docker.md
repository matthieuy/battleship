Docker
======

## Install docker

On debian-like :
```bash
sudo apt-get install docker-ce docker-compose
```

For other system, see docs :
- [Install docker](https://docs.docker.com/engine/installation/)
- [Install docker-compose](https://docs.docker.com/compose/install/)


## Build image

All files are in `app/docker`.

Copy `.env.dist` to `.env` and change variables.

```bash
cd app/docker
docker-compose build
```


## Configure the project

Start the container :
```bash
docker-compose up
```

Configure symfony :
```bash
docker-compose exec php composer install
```
Use `db` for the `database_host` variable


Install libs :

```bash
docker-compose exec php yarn install
docker-compose exec php sf doctrine:migration:migrate
docker-compose exec php sf doctrine:fixtures:load
docker-compose exec php ./bin/js-link
docker-compose exec php sf maba:webpack:compile --env=prod
```


## Run it

Start websocket :
```bash
docker-compose exec php sf gos:websocket:server -a php --env=prod
```
