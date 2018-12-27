[![Build Status](https://travis-ci.org/mooeypoo/MWStew.svg?branch=master)](https://travis-ci.org/mooeypoo/MWStew-builder)
[![GitHub license](https://img.shields.io/badge/license-GPLv2-blue.svg?style=plastic)](https://raw.githubusercontent.com/mooeypoo/MWStew-builder/master/LICENSE)

# MWStew-builder: A backend PHP for building MediaWiki extension files

## Development
This tool is fairly stable, but is currently going through QA and testing, and is continuously developed. Please report any bugs you encounter!

**Feel free to contribute!**

## Usage
This tool is available on packagist. Add it to your project with

`composer install mooeypoo/mwstew-builder`

To create MediaWiki extension files, you need to pass the required parameters to the `Generator`:

```
    // Send $data with the definition of required values for the extension
    $generator = new MWStew\Builder\Generator( $data );
    // The file structure is available by request
    $generator->getFiles();
```

You can then use the Zipper to add the files into a .zip file and output for download:

```
    $tempFolder = dirname( __DIR__ ) . '/temp';
    $zip = new MWStew\Zipper( $tempFolder, 'someName' );
    $zip->addFilesToZip( $generator->getFiles() );
    $zip->download();
```

## Development
If you want to contribute, clone and initialize locally:

1. Clone the repo
2. Run `composer install`
3. Run `composer test-php` to run tests

See [MWStew](https://github.com/mooeypoo/MWStew) for the graphical interface.

## Contribute
This is fully open source tool. It will be hosted so anyone that wants to use it can do so without running the script.

Pull requests are welcome! Please participate and help make this a great tool!

## Authors
Moriel Schottlender (mooeypoo)
