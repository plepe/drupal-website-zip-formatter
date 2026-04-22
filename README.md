# drupal-website-zip-formatter
Serve static websites from ZIP files.

This module adds a field formatter for files, named "Website ZIP print link". The link which is created by this module points to a custom route `/website-zip/123` (where 123 is the File ID). This path serves files from the ZIP file, e.g. `/website-zip/123/dist/app.js` would load the content of the file `dist/app.js` inside the ZIP file. If no path is given, the file 'index.htm' or 'index.html' would be loaded.

## HOWTO
```sh
cd web/modules/custom
git clone https://github.com/plepe/drupal-website-zip-formatter website_zip_formatter
drush en website_zip_formatter
```

The the content type add a new field of type 'file'. Allow files with extension 'zip'.

On "Manage display" of the content type, choose the formatter "Website ZIP print link". You can override the Link text.
