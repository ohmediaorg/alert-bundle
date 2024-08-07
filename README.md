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

Create `templates/bundles/OHMediaAlertBundle/alert.html.twig` and
`templates/bundles/OHMediaAlertBundle/alerts.html.twig`, which are
expected for rendering the WYSIWYG Twig functions `{{ alert(id) }}` and
`{{ alerts() }}`.

The `Alert` entity has required fields `author` and `quote`, and optional
fields `affiliation` and `image`. The `image` is an `OHMedia\FileBundle\Entity\File`
and should be rendered with the `image_tag` Twig function.
