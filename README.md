
# Helpers for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stepanenko3/laravel-helpers.svg?style=flat-square)](https://packagist.org/packages/stepanenko3/laravel-helpers)
[![Total Downloads](https://img.shields.io/packagist/dt/stepanenko3/laravel-helpers.svg?style=flat-square)](https://packagist.org/packages/stepanenko3/laravel-helpers)
[![License](https://poser.pugx.org/stepanenko3/laravel-helpers/license)](https://packagist.org/packages/stepanenko3/laravel-helpers)

## Requirements

- `php: >=8.0`
- `laravel/framework: ^9.0`

## Installation

```bash
# Install the package
composer require stepanenko3/laravel-helpers
```

## Usage

Included functions:

```php
apply_condition(string|int|float $amount, string|null $conditionValue = null): float

camel_to_snake_case(string $input): string

carbon(string|null $parseString = '', string $tz = null): Carbon

class_basename(mixed $class): ?string

class_by_key(string $key, string $service = 'Models'): ?string

cleanConditionValue(string|array $value): string|array

closetags(string $html): string

condition_value(string|int|float $amount, string|null $conditionValue = null): string|int|float

contrast_color(string $hexColor): string

distance(
    string|float|int $latitudeFrom, 
    string|float|int $longitudeFrom, 
    string|float|int $latitudeTo, 
    string|float|int $longitudeTo, 
    int $earthRadius = 6371000
): float|int

domain(): string

dots_to_camel_case(
    string $string, 
    bool $capitalizeFirstCharacter = false
): string

formatMemory(
    float $size, 
    int $level = 0, 
    int $precision = 2, 
    int $base = 1024, 
    bool $asArray = false
): string|array

format_weight(float|int|string $weight): string

from_timestamp(string|int $timestamp): Carbon

get_ascii_key(string $needle, array $haystack = []): ?int

getAspectRatio(int $width, int $height): string

get_by_ascii(string $needle, array $haystack = []): mixed

get_initials(string $name): array|string|null

get_ip(): string

get_order_id(string|int $id): string

get_query_raw(EloquentBuilder|QueryBuilder $builder): string

hashid_decode(string|int $id): array

hashid_encode(string|int $id): string

in_array_wildcard(mixed $needle, array $haystack): bool

ipv4_in_range(mixed $ip, array|string $range): bool

ipv4_match_mask(string $ip, string $network): bool

is_day(
    string|int|null $timestamp = null, 
    float $lat = 50.458124677588046, 
    float $lng = 30.51755711378018
): bool

is_digit(mixed $value): bool

is_night(
    string|int|null $timestamp = null, 
    float $lat = 50.458124677588046, 
    float $lng = 30.51755711378018
): bool

is_query_joined(EloquentBuilder|QueryBuilder $query, string $table): bool

key_by_class(mixed $class): ?string

mb_lcfirst(string $string): string

mb_pathinfo(string $path, string $opt = ''): array|string

mb_ucfirst(string $string): string

model_by_key(string $key): ?string

natural_language_join(array $list, $conjunction = 'and'): string

normalizePrice(string|int|float $price): float

number(string|int|float $value, null|int $decimals = 0): string

num_pad(string|int $value, int $length = 8): string

plural_text(string $text, string $endings): string

// Example usage: str_plural_ru('черновик', 2, '|а|ов'); # Return: черновика
plural_word(string $word, int $count, string $endings): string

remove_query_param(string $url, string $param): string

renderBlade(string $string, array|null $data = null): false|string

rglob(string $pattern): array|false

seo_replace(string $str, array $attributes = []): string

storage_url(string $path, string|null $disk = null): string

str_contains(string $haystack, array $needles): bool

toggle_url(
    string $key, 
    string|null $value = null, 
    string|null $url = null
): string

translit_to_ua(string $text): string

truncate_html(string $text, int $length = 100, array $options = []): string

url_data(string $string, string $action = 'encrypt'): bool|string

user()

utf8ize(array|string $d): string

uuid(int $version = 6): string

validateDate(string $date, string $format = 'Y-m-d'): bool

valueIsPercentage(string $value): bool

valueIsToBeAdded(string $value): bool

valueIsToBeSubtracted(string $value): bool

youtube_id(string $url): string
```

## Credits

- [Artem Stepanenko](https://github.com/stepanenko3)

## Contributing

Thank you for considering contributing to this package! Please create a pull request with your contributions with detailed explanation of the changes you are proposing.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
