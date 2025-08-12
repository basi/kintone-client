# Kintone REST API Client for PHP (Generated)

A PHP client library for Kintone REST API, automatically generated from the official OpenAPI specification.

## Features

- 🚀 Auto-generated from official [Kintone REST API specification](https://github.com/kintone/rest-api-spec)
- 📦 PSR-4 compliant autoloading
- 🔄 Easy updates when API specification changes
- 🎯 Type-safe PHP 8.1+ compatible code

## Project Structure

```
projectRoot/
├── src/                    # Generated PHP client code (composer autoload target)
├── rest-api-spec/          # Kintone API specification (git subtree)
├── scripts/               # Generation and utility scripts
│   ├── generate.php       # Main generation script
│   └── update-spec.sh     # Update API spec from upstream
├── composer.json          # Project configuration with generation scripts
└── README.md             # This file
```

## Installation

```bash
git clone <this-repository>
cd kintone-rest-api-client-php-by-openapi
composer install
```

## Usage

### Generate PHP Client

Generate the PHP client from the OpenAPI specification:

```bash
composer generate
```

This will:
1. Find the latest API specification version
2. Generate PHP client code using OpenAPI Generator
3. Place generated code in the `src/` directory

### Update API Specification

Update to the latest Kintone API specification:

```bash
composer update-spec
```

After updating the specification, regenerate the client:

```bash
composer generate
```

### Using the Generated Client

After generation, you can use the client in your projects:

```php
<?php
require_once 'vendor/autoload.php';

// Use the generated client classes
// (Exact usage depends on the generated code structure)
```

## Requirements

- PHP 8.1 or higher
- Composer
- Java 8 or higher (required for OpenAPI Generator)
- Node.js and npm (for OpenAPI Generator CLI)
- Git

### Installing Java

**Ubuntu/Debian:**
```bash
sudo apt install default-jdk
```

**macOS:**
```bash
brew install openjdk
```

**Windows:**
Download and install from [Adoptium](https://adoptium.net/)

## Development

### Scripts Available

- `composer generate` - Generate PHP client from OpenAPI spec
- `composer update-spec` - Update API specification from upstream
- `composer test` - Run PHPUnit tests
- `composer analyse` - Run PHPStan static analysis

### Manual Specification Update

If you need to manually update the specification:

```bash
git subtree pull --prefix=rest-api-spec https://github.com/kintone/rest-api-spec.git main --squash
```

## License

MIT License

## Contributing

1. Fork the repository
2. Create your feature branch
3. Make your changes
4. Run tests and static analysis
5. Submit a pull request

## Notes

- The `src/` directory is auto-generated and should not be edited manually
- The API specification is managed as a git subtree for easy updates
- Generated code follows PSR-4 autoloading standards
- The project uses the latest available API specification version automatically