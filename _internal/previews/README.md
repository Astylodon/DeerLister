# Previews

This folder contains all the available file previews for Deer Lister.

## Adding a preview

Create a new file with your file preview class by implementing the `FilePreview` interface.

```php
<?php

require_once "FilePreview.php";

class ExamplePreview implements FilePreview
{
    public function doesHandle(string $filename, string $ext): bool
    {
        // Return whether the current preview handles a file
        // either by matching the filename of checking the file extension
        return ...
    }

    public function renderPreview(string $path, string $extension, Twig\Environment $twig): string
    {
        // Render the file preview content (either by using Twig or manually) and return the content
        return ...
    }
}
```

To add the file preview to Deer Lister you must register it, you can do so by adding the following line to `index.php`.

```php
$lister->registerFilePreview("example", ExamplePreview::class);
```

And after it can be enabled in the config file.