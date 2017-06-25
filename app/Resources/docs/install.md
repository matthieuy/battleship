Installation instructions
=========================

### Step 1 : Prerequires

You need :

- PHP >= 5.5.9
- MySQL
- Composer ([see install instructions](https://getcomposer.org/download/))
- NPM ([see install instructions](https://nodejs.org/en/download/package-manager/))
- ZeroMQ

You can install ZeroMQ with this command (on ubuntu/debian like) :

```
sudo apt-get install libzmq3-dev php5-zmq
```

Reload apache/nginx after install it


### Step 2 : Get the app

### Step 3 : Libraries

```
composer install -o
npm install
bower install
php bin/console maba:webpack:compile --env=prod
```

### Step 4 : Database

Populate your database with this command :

```
php bin/console doctrine:migrations:migrate --env=prod
php bin/console doctrine:fixtures:load --env=prod
```

### Step 5 : Launch
