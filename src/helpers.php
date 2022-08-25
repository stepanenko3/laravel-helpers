<?php

use Illuminate\Support\Str;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

if (!function_exists('rglob')) {
    function rglob($pattern)
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

if (!function_exists('readableMemory')) {
    /**
     * readableMemory
     *
     * @param  mixed $memory
     * @param  bool $startFromBytes
     * @param  bool $withUnit
     * @return mixed
     */
    function readableMemory($memory, $startFromBytes = false, $withUnit = true)
    {
        $i = floor(log($memory) / log(1024));
        $sizes = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        if ($startFromBytes) array_unshift($sizes, 'B');

        if ($withUnit)
            return sprintf('%.02F', $memory / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
        else return [
            'memory' => sprintf('%.02F', $memory / pow(1024, $i)) * 1,
            'unit' => $sizes[$i],
        ];
    }
}

if (!function_exists('get_initials')) {
    function get_initials($name)
    {
        $ru = 'АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя';
        $ua = 'АаБбВвГгҐґДдЕеЄєЖжЗзИиIіЇїЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЬьЮюЯя';

        return preg_replace('/(?<=[a-zA-Z' . $ru . $ua . '])./ui', '', $name);
    }
}

if (!function_exists('user')) {
    /**
     * user
     *
     * @return App\Models\User;
     */
    function user()
    {
        if (request()->is('api/*')) {
            $user = auth()->guard('api')->user();
        } else {
            $user = auth()->user();
        }

        return $user;
    }
}

if (!function_exists('contrast_color')) {
    function contrast_color($hexColor)
    {
        $r = hexdec(substr($hexColor, 1, 2));
        $g = hexdec(substr($hexColor, 3, 2));
        $b = hexdec(substr($hexColor, 5, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? 'black' : 'white';
    }
}

if (!function_exists('carbon')) {
    /**
     * carbon
     *
     * @param  mixed $parseString
     * @param  mixed $tz
     * @return Carbon
     */
    function carbon($parseString = '', string $tz = null): Carbon
    {
        return new Carbon($parseString, $tz);
    }
}

if (!function_exists('translit_to_ua')) {
    /**
     * translit_to_ua
     *
     * @param  string $text
     * @return string
     */
    function translit_to_ua(string $text)
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
            'ye' => 'є', 'J' => 'Ї', 'I' => 'І', 'G' => 'Ґ', 'YE' => 'Є'
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
    /**
     * Deal with normal (and irritating) PHP behavior to determine if
     * a value is a non-float positive integer.
     *
     * @param mixed $value
     *
     * @return bool
     */
    function is_digit($value)
    {
        return is_bool($value) ? false : ctype_digit((string) $value);
    }
}

if (!function_exists('validateDate')) {
    /**
     * validateDate
     *
     * @param  string $date
     * @param  string $format
     * @return bool
     */
    function validateDate(string $date, string $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}

if (!function_exists('format_weight')) {
    /**
     * format_weight
     *
     * @param  string|int|float $weight
     * @return string
     */
    function format_weight($weight)
    {
        $string = '';

        $gramFmod = fmod($weight, 1000);
        if ($gramFmod > 0) {
            $string .= $gramFmod . 'g';
        }

        if ($weight > 1000) {
            $string = round($weight / 1000, 0) . 'kg ' . $string;
        }

        return $string;
    }
}

if (!function_exists('mb_lcfirst')) {
    /**
     * mb_lcfirst
     *
     * @param  string $string
     * @return string
     */
    function mb_lcfirst(string $string)
    {
        return mb_strtolower(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}

if (!function_exists('mb_ucfirst')) {
    /**
     * mb_ucfirst
     *
     * @param  string $string
     * @return string
     */
    function mb_ucfirst(string $string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}

if (!function_exists('hashid_encode')) {
    /**
     * hashid_encode
     *
     * @param  string|int $id
     * @return string
     */
    function hashid_encode(string|int $id)
    {
        $hashids = new Hashids();
        return $hashids->encode($id);
    }
}

if (!function_exists('hashid_decode')) {
    /**
     * hashid_decode
     *
     * @param  string|int $id
     * @return array
     */
    function hashid_decode(string|int $id)
    {
        $hashids = new Hashids();
        return $hashids->decode($id);
    }
}

if (!function_exists('num_pad')) {
    /**
     * num_pad
     *
     * @param  string|int $value
     * @param  int $length
     * @return string
     */
    function num_pad(string|int $value, int $length = 8)
    {
        return str_pad($value, $length, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('get_order_id')) {
    /**
     * get_order_id
     *
     * @param  string|int $id
     * @return string
     */
    function get_order_id(string|int $id)
    {
        return num_pad($id);
    }
}

if (!function_exists('get_query_raw')) {
    /**
     * get_query_raw
     *
     * @param  mixed $builder
     * @return string
     */
    function get_query_raw($builder)
    {
        $queryRaw = str_replace(array('?'), array('\'%s\''), $builder->toSql());
        $queryRaw = vsprintf($queryRaw, $builder->getBindings());

        return $queryRaw;
    }
}

if (!function_exists('seo_replace')) {
    /**
     * seo_replace
     *
     * @param  string $str
     * @param  array $attributes
     * @return string
     */
    function seo_replace(string $str, array $attributes = [])
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
    /**
     * is_query_joined
     *
     * @param  mixed $query
     * @param  string $table
     * @return bool
     */
    function is_query_joined($query, $table)
    {
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
    /**
     * normalize price
     *
     * @param $price
     * @return float
     */
    function normalizePrice($price)
    {
        return (is_string($price)) ? (float) $price : $price;
    }
}

if (!function_exists('mb_pathinfo')) {
    function mb_pathinfo($path, $opt = '')
    {
        $separator = ' qq ';
        $path = preg_replace('/[^ ]/u', $separator . "\$0" . $separator, $path);
        if ($opt == '') $pathinfo = pathinfo($path);
        else $pathinfo = pathinfo($path, $opt);

        if (is_array($pathinfo)) {
            $pathinfo2 = $pathinfo;
            foreach ($pathinfo2 as $key => $val) {
                $pathinfo[$key] = str_replace($separator, '', $val);
            }
        } else if (is_string($pathinfo)) $pathinfo = str_replace($separator, '', $pathinfo);
        return $pathinfo;
    }
}

if (!function_exists('toggle_url')) {
    /**
     * toggle_url
     *
     * @param  string $key
     * @param  string|null $value
     * @param  string|null  $url
     * @return string
     */
    function toggle_url(string $key, string|null $value = null, string|null $url = null)
    {
        $url = $url ?? request()->url();

        $params = request()->all();

        if (isset($params[$key])) {
            if ($value) $params[$key] = $value;
            else unset($params[$key]);
        } else $params[$key] = $value;

        $query = http_build_query($params);

        return $url . ($query ? '?' . $query : '');
    }
}

if (!function_exists('remove_query_param')) {
    /**
     * remove_query_param
     *
     * @param  string $url
     * @param  string $param
     * @return string
     */
    function remove_query_param(string $url, string $param)
    {
        $parsed = parse_url($url);
        $query = $parsed['query'];

        parse_str($query, $params);

        unset($params[$param]);
        $query = http_build_query($params);

        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $host = isset($parsed['host']) ? $parsed['host'] : '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $user = isset($parsed['user']) ? $parsed['user'] : '';
        $pass = isset($parsed['pass']) ? ':' . $parsed['pass']  : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed['path']) ? $parsed['path'] : '';
        $query = $query ? '?' . $query : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}

if (!function_exists('distance')) {
    /**
     * distance
     *
     * @param string|float|int $latitudeFrom
     * @param string|float|int $longitudeFrom
     * @param string|float|int $latitudeTo
     * @param string|float|int $longitudeTo
     * @param string|float|int $earthRadius
     *
     * @return float|int
     */
    function distance(
        string|float|int $latitudeFrom,
        string|float|int $longitudeFrom,
        string|float|int $latitudeTo,
        string|float|int $longitudeTo,
        int $earthRadius = 6371000
    ) {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}

if (!function_exists('renderBlade')) {
    function renderBlade($string, $data = null)
    {
        if (!$data) {
            $data = [];
        }

        $data['__env'] = app(\Illuminate\View\Factory::class);

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
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw new ErrorException;
        }

        return ob_get_clean();
    }
}

if (!function_exists('truncate_html')) {
    function truncate_html($text, $length = 100, $options = [])
    {
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
                    } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
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
    /**
     * number
     *
     * @param  mixed $value
     * @param  mixed $decimals
     * @return string
     */
    function number($value, $decimals = 0)
    {
        return number_format($value, $decimals, '.', ' ');
    }
}

if (!function_exists('utf8ize')) {
    /**
     * utf8ize
     *
     * @param  mixed $d
     * @return void
     */
    function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } else if (is_string($d)) {
            return utf8_encode($d);
        }
        return $d;
    }
}

if (!function_exists('get_ascii_key')) {
    /**
     * get_ascii_key
     *
     * @param  string $needle
     * @param  array $haystack
     * @return mixed
     */
    function get_ascii_key(string $needle, array $haystack = [])
    {
        if (!$needle || !$haystack) return;

        $asciiCodeSum = 0;
        $array = str_split($needle);
        foreach ($array as $item) {
            $asciiCodeSum += ord($item);
        }

        return $asciiCodeSum % count($haystack);
    }
}

if (!function_exists('get_by_ascii')) {
    /**
     * get_by_ascii
     *
     * @param  string $needle
     * @param  array $haystack
     * @return mixed
     */
    function get_by_ascii(string $needle, array $haystack = [])
    {
        $asciiKey = get_ascii_key($needle, $haystack);
        if (!$asciiKey && $asciiKey !== 0) return;

        return $haystack[$asciiKey];
    }
}

if (!function_exists('domain')) {
    /**
     * domain
     *
     * @return string
     */
    function domain()
    {
        return parse_url(request()->url())['host'];
    }
}

if (!function_exists('is_day')) {
    /**
     * is_day
     *
     * @param  mixed $timestamp
     * @return bool
     */
    function is_day($timestamp = null, $lat = 50.458124677588046, $lng = 30.51755711378018)
    {
        $timestamp = $timestamp ?? time();

        $suninfo = date_sun_info($timestamp, $lat, $lng);

        $sunrise = from_timestamp($suninfo['sunrise']);
        $sunset = from_timestamp($suninfo['sunset']);

        return from_timestamp($timestamp)->between($sunrise, $sunset);
    }
}

if (!function_exists('is_night')) {
    /**
     * is_night
     *
     * @param  mixed $timestamp
     * @return bool
     */
    function is_night($timestamp = null, $lat = 50.458124677588046, $lng = 30.51755711378018)
    {
        return !is_day($timestamp, $lat, $lng);
    }
}

if (!function_exists('class_basename')) {
    /**
     * class_basename
     *
     * @param  mixed $class
     * @return string
     */
    function class_basename($class)
    {
        if (!$class) return null;

        if (!is_string($class)) {
            $class = get_class($class);
        }

        $key = explode('\\', $class);
        $key = end($key);

        return $key;
    }
}

if (!function_exists('key_by_class')) {
    /**
     * key_by_class
     *
     * @param  mixed $class
     * @return string
     */
    function key_by_class($class)
    {
        $class = class_basename($class);
        if (!$class) return null;

        $key = camel_to_snake_case($class);

        return $key;
    }
}

if (!function_exists('class_by_key')) {
    /**
     * class_by_key
     *
     * @param  string $key
     * @param  string $service
     * @return mixed
     */
    function class_by_key($key, $service = 'Models')
    {
        $model = Str::camel($key);
        $model = 'App\\' . ucfirst($service) . '\\' . ucfirst($model);

        if (class_exists($model)) {
            return $model;
        }

        return null;
    }
}

if (!function_exists('model_by_key')) {
    /**
     * model_by_key
     *
     * @param  string $key
     * @return mixed
     */
    function model_by_key(string $key)
    {
        return class_by_key($key, 'Models');
    }
}

if (!function_exists('plural_word')) {
    /**
     * Example usage: str_plural_ru('черновик', 2, '|а|ов'); # Return: черновика
     *
     * @param string $word
     * @param float $count
     * @param string $endings
     * @return string
     */
    function plural_word(string $word, $count, string $endings)
    {
        $endings = preg_split('/[,\|-]/', $endings);
        $cases = [2, 0, 1, 1, 1, 2];
        $ending = sprintf($endings[($count % 100 > 4 && $count % 100 < 20) ? 2 : $cases[min($count % 10, 5)]], $count);

        return $word . $ending;
    }
}

if (!function_exists('closetags')) {
    /**
     * closetags
     *
     * @param  string $html
     * @return string
     */
    function closetags(string $html)
    {
        #put all opened tags into an array
        preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $html, $result);
        $openedtags = $result[1];

        #put all closed tags into an array
        preg_match_all("#</([a-z]+)>#iU", $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);

        # all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }

        $openedtags = array_reverse($openedtags);

        # close tags
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= "</" . $openedtags[$i] . ">";
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }

        return $html;
    }
}

if (!function_exists('plural_text')) {
    /**
     * Example usage: plural_text('Перенесли 2 черновик%s в товары.', '|а|ов');
     * @param string $text
     * @param string $endings
     * @return string
     */
    function plural_text(string $text, string $endings)
    {
        if (!preg_match('/(\d+)/', $text, $match)) {
            return $text;
        }

        $count = $match[1];
        $endings = preg_split('/[,\|-]/', $endings);
        $cases = [2, 0, 1, 1, 1, 2];
        $ending = sprintf($endings[($count % 100 > 4 && $count % 100 < 20) ? 2 : $cases[min($count % 10, 5)]], $count);

        $text = str_replace('%s', $ending, $text);

        return $text;
    }
}

if (!function_exists('camel_to_snake_case')) {
    /**
     * camel_to_snake_case
     *
     * @param  string $input
     * @return string
     */
    function camel_to_snake_case(string $input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}

if (!function_exists('dots_to_camel_case')) {
    /**
     * dots_to_camel_case
     *
     * @param  string $string
     * @param  bool $capitalizeFirstCharacter
     * @return string
     */
    function dots_to_camel_case(string $string, bool $capitalizeFirstCharacter = false)
    {
        $str = str_replace('.', '', ucwords($string, '.'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }
}

if (!function_exists('in_array_wildcard')) {
    /**
     * in_array_wildcard
     *
     * @param  mixed $needle
     * @param  array $array
     * @return bool
     */
    function in_array_wildcard($needle, array $haystack)
    {
        foreach ($haystack as $pattern) {
            if (\Illuminate\Support\Str::is($pattern, $needle)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('youtube_id')) {
    function youtube_id($url)
    {
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $url, $matches)) {
            $id = $matches[1];
        } else {
            $id = null;
        }

        return $id;
    }
}

if (!function_exists('str_contains')) {
    /**
     * str_contains
     *
     * @param  mixed $haystack
     * @param  mixed $needles
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) return true;
        }

        return false;
    }
}


if (!function_exists('get_ip')) {
    /**
     * get_ip
     *
     * @return string
     */
    function get_ip()
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
    /**
     * ipv4_match_mask
     *
     * @param  string $ip
     * @param  string $network
     * @return bool
     */
    function ipv4_match_mask(string $ip, string $network)
    {
        $ipv4_arr = explode('/', $network);

        if (count($ipv4_arr) == 1) {
            $ipv4_arr[1] = '255.255.255.255';
        }

        $network_long = ip2long($ipv4_arr[0]);

        $x = ip2long($ipv4_arr[1]);
        $mask = long2ip($x) == $ipv4_arr[1] ? $x : 0xffffffff << (32 - $ipv4_arr[1]);
        $ipv4_long = ip2long($ip);

        return ($ipv4_long & $mask) == ($network_long & $mask);
    }
}

if (!function_exists('ipv4_in_range')) {
    /**
     * ipv4_in_range
     *
     * @param  mixed $ip
     * @param  mixed $range
     * @return bool
     */
    function ipv4_in_range($ip, $range)
    {
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
 * Condition helperes
 *
 */

if (!function_exists('valueIsPercentage')) {
    /**
     * check if value is a percentage
     *
     * @param $value
     * @return bool
     */
    function valueIsPercentage($value)
    {
        return (preg_match('/%/', $value) == 1);
    }
}

if (!function_exists('valueIsToBeSubtracted')) {
    /**
     * check if value is a subtract
     *
     * @param $value
     * @return bool
     */
    function valueIsToBeSubtracted($value)
    {
        return (preg_match('/\-/', $value) == 1);
    }
}

if (!function_exists('valueIsToBeAdded')) {
    /**
     * check if value is to be added
     *
     * @param $value
     * @return bool
     */
    function valueIsToBeAdded($value)
    {
        return (preg_match('/\+/', $value) == 1);
    }
}

if (!function_exists('cleanConditionValue')) {
    /**
     * removes some arithmetic signs (%,+,-) only
     *
     * @param $value
     * @return mixed
     */
    function cleanConditionValue($value)
    {
        return str_replace(array('%', '-', '+'), '', $value);
    }
}

if (!function_exists('condition_value')) {
    /**
     * condition_value
     *
     * @param  mixed $amount
     * @param  mixed $conditionValue
     * @return float
     */
    function condition_value($amount, $conditionValue = null)
    {
        if (!$conditionValue)
            return $amount;

        if (valueIsPercentage($conditionValue)) {
            $value = normalizePrice(cleanConditionValue($conditionValue));

            return $amount * ($value / 100);
        } else {
            return normalizePrice(cleanConditionValue($conditionValue));
        }
    }
}

if (!function_exists('from_timestamp')) {
    function from_timestamp($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp);
    }
}

if (!function_exists('apply_condition')) {
    /**
     * apply_condition
     *
     * @param  mixed $amount
     * @param  mixed $conditionValue
     * @return float
     */
    function apply_condition($amount, $conditionValue = null)
    {
        if (!$conditionValue) return $amount < 0 ? 0.00 : $amount;

        $parsedRawValue = condition_value($amount, $conditionValue);

        if (valueIsToBeSubtracted($conditionValue)) {
            $result = (float) ($amount - $parsedRawValue);
        } else {
            $result = (float) ($amount + $parsedRawValue);
        }

        return $result < 0 ? 0.00 : $result;
    }
}


if (!function_exists('storage_url')) {
    function storage_url($path, $disk = null)
    {
        return Storage::disk($disk)->url($path);
    }
}

if (!function_exists('url_data')) {
    function url_data($string, $action = 'encrypt')
    {
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
    function uuid($version = 6)
    {
        $method = 'uuid' . $version;
        return (string) Uuid::$method();
    }
}
