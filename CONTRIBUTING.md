# Questions or bug report

Open a [issue](https://github.com/matthieuy/battleship/issues) for question or bug report


# Contributing

- Fork, then clone the repo:

    git clone git@github.com:your-username/battleship.git

- Make your change (you can use the [docker image](app/Resources/docs/docker.md))
- Check code with :
    ```shell
    ./bin/coke
    ./bin/phpunit
    ```
    or use [git hook](bin/git-hooks/README.md)
- Push to your fork and [submit a pull request](https://github.com/matthieuy/battleship/pull) on the `develop` branch


# Translations

The following languages are supported :
- English

Translation and contribute are welcome !

How to translate (ex: in IT) :
- Fork the repository
- Copy the XLF files `app/Resources/translations` and `src/*Bundle/Resources/translations` 
- Rename extensions files to `it.xlf`
- Change the `target-language` attribute
- Translate file (the `target` balise)
- Edit the `app/config/bundles/translate.yml` file and add `it` in `bazinga_js_translation.active_locales` parameter
- Do a [Pull request](https://github.com/matthieuy/battleship/pulls) on `develop` branch
