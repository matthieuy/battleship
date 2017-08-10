Envoy
=====

[Envoy](http://laravel.com/docs/envoy) is use for deploy the battleship on your server with SSH.

You need to use [supervisor](supervisor.conf) for start/stop/restart the websocket

### Install

Install envoy with composer :

```bash
composer global require laravel/envoy
```

#### Environment

Add `~/.composer/vendor/bin` to your `$PATH`.

For exemple with Bash, add this line in your `~/.bashrc` file :

```bash
export PATH=$PATH:~/.config/composer/vendor/bin
```


### Configuration

Copy `Envoy.config.php.dist` to `Envoy.config.php` and edit it.

You need a SSH key and configure your `~/.ssh/config` (search on the web or open a issue if you need help) 

Check your configuration with this command :
```bash
envoy run ping
```

### Usage

On the first time (ONLY), you need prepare the directory :
```bash
envoy run prepare
```

Now, to update the apps, you can use : 
```bash
envoy run deploy
```
