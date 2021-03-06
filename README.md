[![Build Status](https://travis-ci.org/mooeypoo/MWStew.svg?branch=master)](https://travis-ci.org/mooeypoo/MWStew-builder)
[![Coverage Status](https://coveralls.io/repos/github/mooeypoo/MWStew-builder/badge.svg?branch=master)](https://coveralls.io/github/mooeypoo/MWStew-builder?branch=master)
[![GitHub license](https://img.shields.io/badge/license-GPLv2-blue.svg?style=plastic)](https://raw.githubusercontent.com/mooeypoo/MWStew-builder/master/LICENSE)

# MWStew-builder: A backend PHP library for building MediaWiki extension files

**Feel free to contribute!**

## Usage
This tool is available on packagist. Add it to your project with

```
composer install mooeypoo/mwstew-builder
```

To create MediaWiki extension files, you need to pass the required parameters to the `Generator`:

```php
    // Send $data with the definition of required values for the extension
    $generator = new MWStew\Builder\Generator( $data );
    // The file structure is available by request
    $generator->getFiles();
```
The builder does not write files to the file system, it only builds the structure and lets you handle the rest.

### Expected data

To make an extension bundle, the generator expects data with expected keys. The 'name' key is mandatory, all others are optional:
```php
$data = [
  'name' => (string) Extension name; English only, no spaces (Mandatory)
  'title' => (string) Extension title or display name
  'author' => (string) Extension author
  'version' => (string|number) Extension version
  'description' => (string) A short description for the extension.
  'url' => (string) A URL for the extension
  'license' => (string) License code for the extension. Expected a valid value to be used in composer.json and package.json
  'dev_php' => (bool) Whether the extension should have the base files needed for a PHP development environment.
  'dev_js' => (bool) Whether the extension should have the base files needed for a JavaScript development environment.
  'specialpage_name' => (string) A name for a sepcial page. Must use valid characters for MediaWiki title.
  'specialpage_title' => (string) A title for the special page.
  'specialpage_intro' => (string) A short description or introduction text for the special page. This will appear at the top of the new special page that is created.
  'hooks' => (array) An array of strings representing hooks to be added to the system.
]
```
### Using the files

You can then use the Zipper to add the files into a .zip file and output for download:

```php
    $tempFolder = dirname( __DIR__ ) . '/temp';
    $zip = new MWStew\Builder\Zipper( $tempFolder, 'someName' );
    $zip->addFilesToZip( $generator->getFiles() );
    $zip->download();
```

Or you can create the fils on the file system yourself. To see an example of that, see [MWStew-CLI](https://github.com/mooeypoo/MWStew-CLI) for the command line tool.

## Contribute
This is fully open source tool. Pull requests are welcome! Please participate and help make this a great tool!

If you have suggestions or bug reports, please [submit an issue](https://github.com/mooeypoo/MWStew-builder/issues).

If you want to contribute to the code, clone and initialize locally:

1. Clone the repo
2. Run `composer install`
3. Run `composer run test` to run tests

* See [MWStew](https://github.com/mooeypoo/MWStew) for the graphical interface.
* See [MWStew-CLI](https://github.com/mooeypoo/MWStew-CLI) for the command line tool.

## Changelog
See [CHANGELOG](CHANGELOG.md)

## Authors
Moriel Schottlender (mooeypoo)
