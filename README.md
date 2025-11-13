# Sentry plugin for Craft CMS 4

Pushes Craft CMS errors to [Sentry](https://sentry.io/).

## Installation

### Plugin Store

1. Search for 'Sentry SDK'.
2. Hit install
3. Create a config file as explained below.

### Composer

1. Run: `composer require roelvanhintum/craft-sentry`
2. Hit install in Admin > Settings > Plugins
3. Create a config file as explained below.

## Requirements

- Craft 4.0.0 and up
- PHP 8.0.2 and up

## Configuring Sentry

Create a `config/sentry-sdk.php` config file with the following contents:

```php
<?php

return [
    '*' => [
        'enabled'        => true,
        'anonymous'      => false, // Determines to log user info or not
        'clientDsn'      => getenv('SENTRY_DSN') ?: 'https://example@sentry.io/123456789', // Set as string or use environment variable.
        'clientKey'      => getenv('SENTRY_CLIENT_KEY') ?: 'z987654321a', // https://js.sentry-cdn.com/z987654321a.min.js
        'excludedCodes'  => ['400', '404', '429'],
        'release'        => getenv('SENTRY_RELEASE') ?: null, // Release number/name used by sentry.
        'reportJsErrors' => false,
        'sampleRate'     => 1.0,
        'ignoreErrors'   => [
          // Email link Microsoft Outlook crawler compatibility error
          // cf. https://forum.sentry.io/t/unhandledrejection-non-error-promise-rejection-captured-with-value/14062
          "Non-Error promise rejection captured with value: Object Not Found Matching Id:",
        ]
    ],
];
```

## Credits

Based upon the sentry plugin by [Luke Youell](https://github.com/lukeyouell).
