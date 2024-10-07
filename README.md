# Sylius Mailer Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Enhance your Sylius store's mailing capabilities with this plugin. For now the plugin provides a logging mechanism for all emails sent from your store.

## Installation

1. Require the plugin with Composer:

    ```bash
    composer require setono/sylius-mailer-plugin
    ```

2. Add the plugin to your `config/bundles.php` file:

    ```php
    return [
        // ...
   
        Setono\SyliusMailerPlugin\SetonoSyliusMailerPlugin::class => ['all' => true],
        Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
   
        // ...
    ];
    ```
   
    Remember to add the plugin _before_ the `SyliusGridBundle`.

3. Import the plugin's routing configuration:

    ```yaml
    # config/routes/setono_sylius_mailer.yaml
    setono_sylius_mailer:
        resource: "@SetonoSyliusMailerPlugin/Resources/config/routes.yaml"
    ```

4. Run the database migrations:

    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

## Usage

After installation, the plugin will automatically log all emails sent from your Sylius store. You can view the logged emails in the admin panel under the "Mailer > Sent Emails" section.


[ico-version]: https://poser.pugx.org/setono/sylius-mailer-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-mailer-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-mailer-plugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-mailer-plugin/branch/master/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-mailer-plugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-mailer-plugin
[link-github-actions]: https://github.com/Setono/sylius-mailer-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-mailer-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-mailer-plugin/master
