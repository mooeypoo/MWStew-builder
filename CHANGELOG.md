# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v1.0.1
### Added
- MediaWiki hooks are now available again:
  - Added a build option to update known hooks from the MediaWiki API and store the information in the distributed package.
  - Known hooks would be written with a function that has their signature and details
  - Unknown hooks are built through a standalone template into the extension file code.
- Add... a changelog!

### Changed
- Generate i18n files from twig templates rather than code
- Generate extension.json from twig templates rather than code
- Create Structure class to define file-structure with corresponding templates
- Simplify the Generator and use file structures for flexibility

### Removed
- Removed redundant code functionality to create extension.json data

## v1.0.1
### Added
- Add CODE_OF_CONDUCT output

## v1.0.0
### Changed
- Add tests and coverage reports with coveralls
- Remove unused hook functionality. A new functionality is planned for the future.
- Change test command to `composer run test`

## Prior versions
Prior versions to v1.0.0 are unstable and were used for migrating the code from the previous [MWStew](https://github.com/mooeypoo/MWStew) repo. Do not use these versions.
