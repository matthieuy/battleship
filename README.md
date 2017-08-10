Battleship
==========

[![GitHub issues](https://img.shields.io/github/issues/matthieuy/battleship.svg)](https://github.com/matthieuy/battleship/issues)
[![Latest Stable Version](https://poser.pugx.org/matthieuy/battleship/v/stable)](https://packagist.org/packages/matthieuy/battleship)
[![License](https://poser.pugx.org/matthieuy/battleship/license)](https://packagist.org/packages/matthieuy/battleship)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5a177132-6c18-4b78-a45e-5e9de1f7d2f3/mini.png)](https://insight.sensiolabs.com/projects/5a177132-6c18-4b78-a45e-5e9de1f7d2f3)

This is a battleship game in real time.

You can play up to 12 players or AI on the same grid.

A weapon system is integrated in the game for more fun.

It use the awesome [WebSocketBundle](https://github.com/GeniusesOfSymfony/WebSocketBundle) with Ratchet and AutoBahn.


Features
========

- Generate grid
- Real time (WebSocket)
- Play up to 12 players on the same grid
- AI players
- Weapons
- Bonus and inventory
- Animate shot
- Translations
- Chat
- Notifications :
  - Mail
  - SMS
  - Discord :
    - Webhook
    - Bot


Installation
============

See [Installation instructions](app/Resources/docs/install.md) or use [docker](app/Resources/docs/docker.md)


Contribute or bug report
========================

See the [guideline](CONTRIBUTING.md)


Tests
=====

```shell
./bin/coke
./bin/phpunit
```


TODO / CHANGELOG
====

* See the complete [TODO](TODO.md)
* See the [CHANGELOG](CHANGELOG.md)

Credits
=======

- Lead developer : Matthieu YK
- Graphism : Chabull, [Clint Bellanger](http://clintbellanger.net/), Static, contributors of [Game-icons](http://game-icons.net/)
- Beta testers : GeekaRiom team


License
=======

Battleship is licensed under the MIT License - see the [LICENSE file](LICENSE) for details
