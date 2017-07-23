Installation instructions
=========================

### Step 1 : Prerequires

You need :

- PHP >= 5.5.9
- GD (`sudo apt-get install php-gd`)
- MySQL
- Composer ([see install instructions](https://getcomposer.org/download/))
- NPM ([see install instructions](https://nodejs.org/en/download/package-manager/))
- Yarn (`npm install -g yarn`)
- ZeroMQ

You can install ZeroMQ with this command (on ubuntu/debian like) :

```
sudo apt-get install libzmq3-dev php-zmq
```

Reload apache/nginx after install it


### Step 2 : Get the app

-  If you have git :

    ```
    git clone https://github.com/matthieuy/battleship.git
    cd battleship
    ```
- If you don't have git :
 
    [download the app](https://github.com/matthieuy/battleship/releases) and unzip it 


### Step 3 : Libraries

```
composer install -o
yarn install
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

Launch the websocket server :
```
php bin/console gos:websocket:server --env=prod
```

See [nginx configuration](nginx.md) to configure nginx

See [ssl configuration](ssl.md) if you want to use secure connection to websocket.
