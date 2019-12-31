# Changelog

## v1.3.1 - 03.04.2019

### Bugfix
 * Fixed checking for existing keys

## v1.3 - 08.03.2019

### Feature
 * Added Redis Cache

## v1.1 - 03.08.2018

### Breaking Changes
 * Cache::get($key) doesn't throw an exception anymore, when key was not found.
   Instead it returns null now.
