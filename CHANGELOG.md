# Craft Sentry Changelog

## 3.0.0 - 2024-06-13

### Changed

- Added ignoreErrors option for use with sentry js client

## 3.0.0-beta.1 - 2024-03-29

### Changed

- Added craft 5 support
- Moved to Sentry PHP SDK 4.x
- Moved to latest Sentry Browser SDK
- Client settings are removed in favor of Sentry's "Loader Script" settings page.
- Removed `performanceMonitoring` and `autoSessionTracking` settings.

## 2.0.1 - 2022-09-16

### Changed

- If the exception is a Twig Runtime exception, use the previous one instead. Thanks to @kringkaste

## 2.0.0 - 2022-07-13

### Changed

- Fixed craft 4 support
- Use `CRAFT_APP_ID` env instead of `APP_ID` env.
- Use `CRAFT_ENVIRONMENT` env instead of the `CRAFT_ENVIRONMENT` constant.

## 2.0.0-beta.1 - 2022-03-08

### Changed

- Added craft 4 support

## 1.1.2 - 2021-05-11

### Change

- Make `performanceMonitoring` optional

## 1.1.1 - 2021-05-07

### Added

- Added `autoSessionTracking` support.

### Change

- Updated sentry js client from 5.29.2 to 6.3.5

## 1.1.0 - 2021-04-15

### Added

- Console errors are now captured.

## 1.0.12 - 2021-03-11

- Added sampleRate config option. Thanks to @brianjhanson

## 1.0.11 - 2021-01-15

- Added getSentry method and docblock. Thanks to @joshuabaker

## 1.0.10.1 - 2021-01-07

- Fix PHP parsing error. Thanks to @cgivord

## 1.0.10 - 2020-12-31

- Added CSP nonce support using https://github.com/roelvanhintum/craft-csp
- Updated Sentry JS SDK

## 1.0.9 - 2020-12-17

- Implement Sentry JavaScript SDK for site front end requests. Thanks to @jamesmacwhite
- Init Sentry JS SDK with debug option mapped to Craft devMode setting
- New `reportJsErrors` config option for enabling JS error reporting
- Removed old reference in README about Sentry being disabled with devMode on (no longer valid)

## 1.0.8.1 - 2020-11-03

### Fixed

- Fixed composer.json for composer 2

## 1.0.8 - 2020-11-02

### Changed

- Updated sentry/sdk to 3.1
- Use the default integrations as much as possible for more info

## 1.0.7 - 2020-10-16

### Changed

- Don't do anything until EVENT_AFTER_LOAD_PLUGINS. Thanks to @jamesedmonston

## 1.0.6 - 2020-01-17

### Fixes

- Prevent errors during plugin install. Thanks to @boboldehampsink

## 1.0.5 - 2020-01-09

### Changed

- Updated sentry/sdk to 2.1.0

## 1.0.4 - 2020-01-09

### Added

- Let sentry handle craft console errors

## 1.0.3 - 2020-01-09

### Change

- Let craft handle craft errors again for excludedCodes support.
- Depend on enabled setting not on devMode

## 1.0.2 - 2019-12-13

### Fixed

- Downgraded sentry/sdk to prevent install errors

## 1.0.1 - 2019-12-12

### Change

- Populate all captures with additional data.
- Don't use craft handled error events.

## 1.0.0 - 2019-11-26

### Added

- Stable release

## 1.0.0-beta.1 - 2019-11-25

### Added

- Initial Release
