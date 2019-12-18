
# Karls Reference Image Bundle for Unite-Cms

Provides a new Field Type for handling image references.

## Installation

via composer
```
    composer require karls/reference-image-bundle
```

in config/bundles.php

```
Karls\ReferenceImageBundle\KarlsReferenceImageBundle::class => ['all' => true],
```

in config/packages/assets.yaml

```yaml
framework:
    assets:
        packages:
            KarlsReferenceImageBundle:
                json_manifest_path: "%kernel.project_dir%/public/bundles/karlsreferenceimage/manifest.json"
                base_path: '/bundles/karlsreferenceimage'

```

## Usage


#### Reference Image
Unite-CMS doesn't support references with images as a preview. Now you can reference a _domain_ and _content_type_ and also a `preview_image`. The `preview_image` setting defines which field of the _content_type_ is a image and should be displayed when selected. The config should look like this (similar as normal reference):

```javascript
{
    "type": "reference_image",
    "settings": {
        ... ,
        "preview_image": "image"
    }
}
```
All other configs are the same as a normal reference