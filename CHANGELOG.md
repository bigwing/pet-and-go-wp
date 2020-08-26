# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2020-08-25

### Added
- Transient caching with [wp-cache-remember](https://github.com/stevegrunwell/wp-cache-remember)
- [Psalm](https://psalm.dev) for static code checking.

### Changed
- Fixed mismatched license in composer.json to use MIT.
-

### Removed
- [Mozart](https://packagist.org/packages/coenjacobs/mozart) for Composer package management.

## [1.0.0] - 2020-08-25
Initial release 🚀

### Added
- Interface for petandgo.com API.
- Client interface for enabling multiple transports.
- PSR-compliant logger using Monolog.
- XML parser for responses with SimpleXML.
