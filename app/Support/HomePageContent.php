<?php

namespace App\Support;

class HomePageContent
{
    public static function all(): array
    {
        return config('home', []);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return data_get(self::all(), $key, $default);
    }

    public static function text(string $key, array $replace = [], string $default = ''): string
    {
        $value = (string) self::get($key, $default);

        foreach ($replace as $name => $replacement) {
            $value = str_replace(':' . $name, (string) $replacement, $value);
        }

        return $value;
    }
}
