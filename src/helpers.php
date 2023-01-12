<?php

use App\Models\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\{Blade, Storage};
use Illuminate\Support\Str;
use Illuminate\View\Factory;
use Ramsey\Uuid\Uuid;

if (!function_exists('getAspectRatio')) {
    function getAspectRatio(int $width, int $height): string
    {
        // search for greatest common divisor
        $greatestCommonDivisor = static function ($width, $height) use (&$greatestCommonDivisor) {
            return ($width % $height) ? $greatestCommonDivisor($height, $width % $height) : $height;
        };

        $divisor = $greatestCommonDivisor($width, $height);

        return $width / $divisor . ':' . $height / $divisor;
    }
}

if (!function_exists('rglob')) {
    function rglob(string $pattern): array | false
    {
        $files = glob($pattern);

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge(
                [],
                ...[$files, rglob($dir . '/' . basename($pattern))],
            );
        }

        return $files;
    }
}

if (!function_exists('formatMemory')) {
    function formatMemory(
        float $size,
        int $level = 0,
        int $precision = 2,
        int $base = 1024,
        bool $asArray = false,
    ): string | array {
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $times = floor(log($size, $base));

        $memory = sprintf('%.' . $precision . 'f', $size / pow($base, ($times + $level)));
        $unit = $unit[$times + $level];

        if ($asArray) {
            return [
                'memory' => $memory,
                'unit' => $unit,
            ];
        }

        return $memory . ' ' . $unit;
    }
}

if (!function_exists('get_initials')) {
    function get_initials(
        string $name,
    ): array | string | null {
        $ru = 'АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя';
        $ua = 'АаБбВвГгҐґДдЕеЄєЖжЗзИиIіЇїЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЬьЮюЯя';

        return preg_replace('/(?<=[a-zA-Z' . $ru . $ua . '])./ui', '', $name);
    }
}

if (!function_exists('user')) {
    function user()
    {
        return request()->is('api/*')
            ? auth()->guard('api')->user()
            : auth()->user();
    }
}

if (!function_exists('contrast_color')) {
    function contrast_color(string $hexColor): string
    {
        $r = hexdec(substr($hexColor, 1, 2));
        $g = hexdec(substr($hexColor, 3, 2));
        $b = hexdec(substr($hexColor, 5, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? 'black' : 'white';
    }
}

if (!function_exists('carbon')) {
    function carbon(DateTimeInterface | string | null $parseString = '', string $tz = null): Carbon
    {
        return new Carbon($parseString, $tz);
    }
}

if (!function_exists('translit_to_ua')) {
    function translit_to_ua(string $text): string
    {
        $alphabet = [
            'a' => 'а', 'b' => 'б', 'v' => 'в', 'g' => 'г', 'd' => 'д', 'e' => 'е', 'yo' => 'ё',
            'j' => 'ж', 'z' => 'з', 'i' => 'и', 'i' => 'й', 'k' => 'к', 'l' => 'л', 'm' => 'м',
            'n' => 'н', 'o' => 'о', 'p' => 'п', 'r' => 'р', 's' => 'с', 't' => 'т', 'y' => 'у',
            'f' => 'ф', 'h' => 'х', 'c' => 'ц', 'ch' => 'ч', 'sh' => 'ш', 'sh' => 'щ', 'i' => 'ы',
            'e' => 'е', 'u' => 'у', 'ya' => 'я', 'A' => 'А', 'B' => 'Б', 'V' => 'В', 'G' => 'Г',
            'D' => 'Д', 'E' => 'Е', 'Yo' => 'Ё', 'J' => 'Ж', 'Z' => 'З', 'I' => 'И', 'I' => 'Й',
            'K' => 'К', 'L' => 'Л', 'M' => 'М', 'N' => 'Н', 'O' => 'О', 'P' => 'П', 'R' => 'Р',
            'S' => 'С', 'T' => 'Т', 'Y' => 'Ю', 'F' => 'Ф', 'H' => 'Х', 'C' => 'Ц', 'Ch' => 'Ч',
            'Sh' => 'Ш', 'Sh' => 'Щ', 'I' => 'Ы', 'E' => 'Е', 'U' => 'У', 'Ya' => 'Я', '\'' => 'ь',
            '\'' => 'Ь', '\'\'' => 'ъ', '\'\'' => 'Ъ', 'j' => 'ї', 'i' => 'и', 'g' => 'ґ',
            'ye' => 'є', 'J' => 'Ї', 'I' => 'І', 'G' => 'Ґ', 'YE' => 'Є',
        ];

        return str_ireplace(
            array_keys($alphabet),
            array_values($alphabet),
            str_ireplace(
                ['Ye', 'Yi', 'Y', 'Yu', 'Ya', 'ye', 'yi', 'y', 'yu', 'ya'],
                ['Є', 'Ї', 'Й', 'Ю', 'Я', 'є', 'ї', 'й', 'ю', 'я'],
                $text,
            )
        );
    }
}

if (!function_exists('is_digit')) {
    function is_digit(mixed $value): bool
    {
        return !is_bool($value) && ctype_digit((string) $value);
    }
}

if (!function_exists('validateDate')) {
    function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}

if (!function_exists('format_weight')) {
    function format_weight(float | int | string $weight): string
    {
        $string = '';

        $gramFmod = fmod($weight, 1000);
        if ($gramFmod > 0) {
            $string .= $gramFmod . 'g';
        }

        if ($weight > 1000) {
            $string = round($weight / 1000) . 'kg ' . $string;
        }

        return $string;
    }
}

if (!function_exists('mb_lcfirst')) {
    function mb_lcfirst(string $string): string
    {
        return mb_strtolower(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst(string $string): string
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}

if (!function_exists('hashid_encode')) {
    function hashid_encode(string | int $id): string
    {
        $hashids = new Hashids();

        return $hashids->encode($id);
    }
}

if (!function_exists('hashid_decode')) {
    function hashid_decode(string | int $id): array
    {
        $hashids = new Hashids();

        return $hashids->decode($id);
    }
}

if (!function_exists('num_pad')) {
    function num_pad(string | int $value, int $length = 8): string
    {
        return str_pad($value, $length, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('get_order_id')) {
    function get_order_id(string | int $id): string
    {
        return num_pad($id);
    }
}

if (!function_exists('get_query_raw')) {
    function get_query_raw(EloquentBuilder | QueryBuilder $builder): string
    {
        $queryRaw = str_replace(['?'], ['\'%s\''], $builder->toSql());

        return vsprintf($queryRaw, $builder->getBindings());
    }
}

if (!function_exists('seo_replace')) {
    function seo_replace(string $str, array $attributes = []): string
    {
        if ($attributes) {
            $attributes = collect($attributes);

            $search = $attributes
                ->keys()
                ->map(fn ($attr) => ':' . $attr)
                ->toArray();

            $replace = $attributes
                ->values()
                ->toArray();

            $str = str_ireplace($search, $replace, $str);
        }

        $symbols = [',', '.', ':', ';', '!', '|', '-'];
        $replace = [];

        foreach ($symbols as $symbol) {
            $replace[' ' . $symbol] = $symbol;
        }

        foreach ($symbols as $symbol) {
            $replace[$symbol . $symbol] = $symbol;
        }

        foreach ($symbols as $symbol) {
            foreach ($symbols as $subSymbol) {
                if ($subSymbol != $symbol) {
                    $replace[$symbol . $subSymbol] = $symbol;
                }
            }
        }

        for ($i = 0; $i <= 2; $i++) {
            $str = str_ireplace(array_keys($replace), array_values($replace), $str);
        }

        $str = str_ireplace('  ', ' ', $str);

        return trim($str, " \t\n\r\0\x0B" . implode('', $symbols));
    }
}

if (!function_exists('is_query_joined')) {
    function is_query_joined(
        EloquentBuilder | QueryBuilder $query,
        string $table,
    ): bool {
        $joins = $query->getQuery()->joins;
        if ($joins == null) {
            return false;
        }
        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('normalizePrice')) {
    function normalizePrice(string | int | float $price): float
    {
        return (is_string($price)) ? (float) $price : $price;
    }
}

if (!function_exists('mb_pathinfo')) {
    function mb_pathinfo(
        string $path,
        string $opt = '',
    ): array | string {
        $separator = ' qq ';
        $path = preg_replace('/[^ ]/u', $separator . '$0' . $separator, $path);
        if ($opt == '') {
            $pathinfo = pathinfo($path);
        } else {
            $pathinfo = pathinfo($path, $opt);
        }

        if (is_array($pathinfo)) {
            $pathinfo2 = $pathinfo;
            foreach ($pathinfo2 as $key => $val) {
                $pathinfo[$key] = str_replace($separator, '', $val);
            }
        } elseif (is_string($pathinfo)) {
            $pathinfo = str_replace($separator, '', $pathinfo);
        }

        return $pathinfo;
    }
}

if (!function_exists('toggle_url')) {
    function toggle_url(
        string $key,
        string | null $value = null,
        string | null $url = null,
    ): string {
        $url = $url ?? request()->url();

        $params = request()->all();

        if (isset($params[$key])) {
            if ($value) {
                $params[$key] = $value;
            } else {
                unset($params[$key]);
            }
        } else {
            $params[$key] = $value;
        }

        $query = http_build_query($params);

        return $url . ($query ? '?' . $query : '');
    }
}

if (!function_exists('remove_query_param')) {
    function remove_query_param(
        string $url,
        string $param,
    ): string {
        $parsed = parse_url($url);
        $query = $parsed['query'];

        parse_str($query, $params);

        unset($params[$param]);
        $query = http_build_query($params);

        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $user = $parsed['user'] ?? '';
        $pass = isset($parsed['pass']) ? ':' . $parsed['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = $parsed['path'] ?? '';
        $query = $query ? '?' . $query : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}

if (!function_exists('distance')) {
    function distance(
        string | float | int $latitudeFrom,
        string | float | int $longitudeFrom,
        string | float | int $latitudeTo,
        string | float | int $longitudeTo,
        int $earthRadius = 6371000,
    ): float | int {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2)
            + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return $angle * $earthRadius;
    }
}

if (!function_exists('renderBlade')) {
    function renderBlade(
        string $string,
        array | null $data = null,
    ): false | string {
        if (!$data) {
            $data = [];
        }

        $data['__env'] = app(Factory::class);

        $php = Blade::compileString($string);

        $obLevel = ob_get_level();
        ob_start();
        extract($data, EXTR_SKIP);

        try {
            eval('?' . '>' . $php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        } catch (Throwable) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw new ErrorException;
        }

        return ob_get_clean();
    }
}

if (!function_exists('truncate_html')) {
    function truncate_html(
        string $text,
        int $length = 100,
        array $options = [],
    ): string {
        $default = [
            'ending' => '...',
            'exact' => true,
            'html' => false,
        ];

        $options = array_merge($default, $options);
        extract($options);

        if ($html) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }

            $totalLength = mb_strlen(strip_tags($ending));
            $openTags = [];
            $truncate = '';

            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];

                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);

                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }
                if ($totalLength >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
            }
        }

        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                if ($html) {
                    $bits = mb_substr($truncate, $spacepos);
                    preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                    if (!empty($droppedTags)) {
                        foreach ($droppedTags as $closingTag) {
                            if (!in_array($closingTag[1], $openTags)) {
                                array_unshift($openTags, $closingTag[1]);
                            }
                        }
                    }
                }
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }
        $truncate .= $ending;

        if ($html) {
            foreach ($openTags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }
}

if (!function_exists('number')) {
    function number(
        string | int | float $value,
        null | int $decimals = 0,
    ): string {
        return number_format($value, $decimals, '.', ' ');
    }
}

if (!function_exists('utf8ize')) {
    function utf8ize(array | string $d): string | array
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } elseif (is_string($d)) {
            return utf8_encode($d);
        }

        return $d;
    }
}

if (!function_exists('get_ascii_key')) {
    function get_ascii_key(
        string $needle,
        array $haystack = [],
    ): ?int {
        if (!$needle || !$haystack) {
            return null;
        }

        $asciiCodeSum = 0;
        $array = str_split($needle);
        foreach ($array as $item) {
            $asciiCodeSum += ord($item);
        }

        return $asciiCodeSum % count($haystack);
    }
}

if (!function_exists('get_by_ascii')) {
    function get_by_ascii(
        string $needle,
        array $haystack = [],
    ): mixed {
        $asciiKey = get_ascii_key($needle, $haystack);
        if (!$asciiKey && $asciiKey !== 0) {
            return null;
        }

        return $haystack[$asciiKey];
    }
}

if (!function_exists('domain')) {
    function domain(): string
    {
        return parse_url(request()->url())['host'];
    }
}

if (!function_exists('is_day')) {
    function is_day(
        string | int | null $timestamp = null,
        float $lat = 50.458124677588046,
        float $lng = 30.51755711378018,
    ): bool {
        $timestamp = $timestamp ?? time();

        $suninfo = date_sun_info($timestamp, $lat, $lng);

        $sunrise = from_timestamp($suninfo['sunrise']);
        $sunset = from_timestamp($suninfo['sunset']);

        return from_timestamp($timestamp)->between($sunrise, $sunset);
    }
}

if (!function_exists('is_night')) {
    function is_night(
        string | int | null $timestamp = null,
        float $lat = 50.458124677588046,
        float $lng = 30.51755711378018,
    ): bool {
        return !is_day($timestamp, $lat, $lng);
    }
}

if (!function_exists('class_basename')) {
    function class_basename(mixed $class): ?string
    {
        if (!$class) {
            return null;
        }

        if (!is_string($class)) {
            $class = get_class($class);
        }

        $key = explode('\\', $class);

        return (string) end($key);
    }
}

if (!function_exists('key_by_class')) {
    function key_by_class(mixed $class): ?string
    {
        $class = class_basename($class);

        if (!$class) {
            return null;
        }

        return camel_to_snake_case($class);
    }
}

if (!function_exists('class_by_key')) {
    function class_by_key(
        string $key,
        string $service = 'Models',
    ): ?string {
        $model = Str::camel($key);
        $model = 'App\\' . ucfirst($service) . '\\' . ucfirst($model);

        if (class_exists($model)) {
            return (string) $model;
        }

        return null;
    }
}

if (!function_exists('model_by_key')) {
    function model_by_key(string $key): ?string
    {
        return class_by_key($key);
    }
}

if (!function_exists('plural_word')) {
    // Example usage: str_plural_ru('черновик', 2, '|а|ов'); # Return: черновика
    function plural_word(
        string $word,
        int $count,
        string $endings,
    ): string {
        $endings = preg_split('/[,\|-]/', $endings);
        $cases = [2, 0, 1, 1, 1, 2];
        $ending = sprintf($endings[($count % 100 > 4 && $count % 100 < 20) ? 2 : $cases[min($count % 10, 5)]], $count);

        return $word . $ending;
    }
}

if (!function_exists('closetags')) {
    function closetags(string $html): string
    {
        //put all opened tags into an array
        preg_match_all('#<([a-z]+)( .*)?(?!/)>#iU', $html, $result);
        $openedtags = $result[1];

        //put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);

        // all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }

        $openedtags = array_reverse($openedtags);

        // close tags
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</' . $openedtags[$i] . '>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }

        return $html;
    }
}

if (!function_exists('plural_text')) {
    // Example usage: plural_text('Перенесли 2 черновик%s в товары.', '|а|ов');
    function plural_text(
        string $text,
        string $endings,
    ): string {
        if (!preg_match('/(\d+)/', $text, $match)) {
            return $text;
        }

        $count = $match[1];
        $endings = preg_split('/[,\|-]/', $endings);
        $cases = [2, 0, 1, 1, 1, 2];
        $ending = sprintf($endings[($count % 100 > 4 && $count % 100 < 20) ? 2 : $cases[min($count % 10, 5)]], $count);

        return str_replace('%s', $ending, $text);
    }
}

if (!function_exists('camel_to_snake_case')) {
    function camel_to_snake_case(
        string $input,
    ): string {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}

if (!function_exists('dots_to_camel_case')) {
    function dots_to_camel_case(
        string $string,
        bool $capitalizeFirstCharacter = false,
    ): string {
        $str = str_replace('.', '', ucwords($string, '.'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }
}

if (!function_exists('in_array_wildcard')) {
    function in_array_wildcard(
        mixed $needle,
        array $haystack,
    ): bool {
        foreach ($haystack as $pattern) {
            if (Str::is($pattern, $needle)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('youtube_id')) {
    function youtube_id(string $url): string
    {
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } elseif (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } elseif (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } else {
            $id = null;
        }

        return $id;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(
        string $haystack,
        array $needles,
    ): bool {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('get_ip')) {
    function get_ip(): string
    {
        $ip = request()->ip();

        // if (in_array_wildcard($ip, ['127.0.0.1', '::1', '192.168.*'])) {
        //     try {
        //         $externalContent = file_get_contents('http://checkip.dyndns.com/');
        //         preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
        //         $ip = $m[1];
        //     } catch (Exception $e) {
        //         $ip = trim(shell_exec('dig +short myip.opendns.com @resolver1.opendns.com'));
        //     }
        // }

        return $ip;
    }
}

if (!function_exists('ipv4_match_mask')) {
    function ipv4_match_mask(
        string $ip,
        string $network,
    ): bool {
        $ipv4_arr = explode('/', $network);

        if (count($ipv4_arr) == 1) {
            $ipv4_arr[1] = '255.255.255.255';
        }

        $network_long = ip2long($ipv4_arr[0]);

        $x = ip2long($ipv4_arr[1]);
        $mask = long2ip($x) == $ipv4_arr[1] ? $x : 0xFFFFFFFF << (32 - $ipv4_arr[1]);
        $ipv4_long = ip2long($ip);

        return ($ipv4_long & $mask) == ($network_long & $mask);
    }
}

if (!function_exists('ipv4_in_range')) {
    function ipv4_in_range(
        mixed $ip,
        array | string $range,
    ): bool {
        if (is_array($range)) {
            foreach ($range as $iprange) {
                if (ipv4_in_range($ip, $iprange)) {
                    return true;
                }
            }

            return false;
        }

        if (!str_contains($range, '-') && str_contains($range, '*')) {
            $range = str_replace('*', '0', $range) . '-' . str_replace('*', '255', $range);
        }

        if (count($twoIps = explode('-', $range)) == 2) {
            $ip1 = ip2long($twoIps[0]);
            $ip2 = ip2long($twoIps[1]);

            return ip2long($ip) >= $ip1 && ip2long($ip) <= $ip2;
        }

        return ipv4_match_mask($ip, $range);
    }
}

/**
 * Conditional helpers
 */
if (!function_exists('valueIsPercentage')) {
    function valueIsPercentage(string $value): bool
    {
        return preg_match('/%/', $value) == 1;
    }
}

if (!function_exists('valueIsToBeSubtracted')) {
    function valueIsToBeSubtracted(string $value): bool
    {
        return preg_match('/\-/', $value) == 1;
    }
}

if (!function_exists('valueIsToBeAdded')) {
    function valueIsToBeAdded(string $value): bool
    {
        return preg_match('/\+/', $value) == 1;
    }
}

if (!function_exists('cleanConditionValue')) {
    function cleanConditionValue(string | array $value): string | array
    {
        return str_replace(['%', '-', '+'], '', $value);
    }
}

if (!function_exists('condition_value')) {
    function condition_value(
        string | int | float $amount,
        string | null $conditionValue = null,
    ): string | int | float {
        if (!$conditionValue) {
            return $amount;
        }

        if (valueIsPercentage($conditionValue)) {
            $value = normalizePrice(cleanConditionValue($conditionValue));

            return $amount * ($value / 100);
        } else {
            return normalizePrice(cleanConditionValue($conditionValue));
        }
    }
}

if (!function_exists('apply_condition')) {
    function apply_condition(
        string | int | float $amount,
        string | null $conditionValue = null,
    ): float {
        if (!$conditionValue) {
            return $amount < 0 ? 0.00 : $amount;
        }

        $parsedRawValue = condition_value($amount, $conditionValue);

        $result = valueIsToBeSubtracted($conditionValue)
            ? (float) ($amount - $parsedRawValue)
            : (float) ($amount + $parsedRawValue);

        return $result < 0 ? 0.00 : $result;
    }
}

if (!function_exists('from_timestamp')) {
    function from_timestamp(
        string | int $timestamp,
    ): Carbon {
        return Carbon::createFromTimestamp($timestamp);
    }
}

if (!function_exists('storage_url')) {
    function storage_url(
        string $path,
        string | null $disk = null,
    ): string {
        return Storage::disk($disk)->url($path);
    }
}

if (!function_exists('url_data')) {
    function url_data(
        string $string,
        string $action = 'encrypt',
    ): bool | string {
        $encrypt_method = 'AES-256-CBC';
        $secret_key = config('app.key');
        $secret_iv = md5(config('app.key'));
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return match ($action) {
            'encrypt' => base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv)),
            'decrypt' => openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv),
        };
    }
}

if (!function_exists('uuid')) {
    function uuid(
        int $version = 6,
    ): string {
        $method = 'uuid' . $version;

        return (string) Uuid::$method();
    }
}

if (!function_exists('natural_language_join')) {
    function natural_language_join(array $list, $conjunction = 'and'): string
    {
        $last = array_pop($list);

        if ($list) {
          return implode(', ', $list) . ' ' . $conjunction . ' ' . $last;
        }

        return $last;
    }
}

if (!function_exists('random_code_chars')) {
    /**
     * Function providing random chars pool for the random_code generation.
     *
     * @param bool $only_letters
     * @return array
     */
    function random_code_chars(bool $only_letters = false): array
    {
        $letters = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',
            'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];
        $numbers = [
            '1', '2', '3', '4', '5', '6', '7', '8', '9',
        ];

        return $only_letters ? $letters : array_merge($numbers, $letters);
    }
}

if (!function_exists('random_code')) {
    /**
     * Function to generate random code strings (upper case) - does not have characters that can be mistaken (0/O, l/1) etc
     *
     *
     * Uniqueness depending on code length (assuming random_code_chars returns 33 characters):
     * 7 -> 30,995,231,256 available values
     * 6 ->    939,249,432
     * 5 ->     28,462,104
     * 4 ->        862,488
     *
     * @param int $length
     *            number of characters in the generated string
     * @return string a new string is created with random characters of the desired length
     */
    function random_code(int $length = 7): string
    {
        srand(microtime(true) * 1000000);

        // our array add all letters and numbers if you wish
        $chars = random_code_chars();

        $letters = random_code_chars(true);
        $randstr = $letters[rand(0, count($letters) - 1)];

        for ($rand = 2; $rand <= $length; $rand++) {
            $random = rand(0, count($chars) - 1);
            $randstr .= $chars[$random];
        }

        return $randstr;
    }
}
