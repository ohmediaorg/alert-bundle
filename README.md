# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/alert-bundle"
}
```

Then run `composer require ohmediaorg/alert-bundle:dev-main`.

Import the routes in `config/routes.yaml`:

```yaml
oh_media_alert:
    resource: '@OHMediaAlertBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.

# Frontend

The frontend relies on Bootstrap's Alert component (Sass and JS). The output
utilizes the `.alert` class with an `.alert-bar` class for any customizations.

Just place `{{ alert_bar() }}` where needed.
