<?php

/**
 * Class Host
 */
class Host
{
    /**
     * @return string
     */
    public static function domain(): string
    {
        return filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
    }

    /**
     * @return string
     */
    public static function protocol(): string
    {
        return sprintf('http%s://',  self::isSecure() ? 's' : '');
    }

    public static function isSecure(): bool
    {
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        if (!$isHttps)
            $isHttps = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';

        return $isHttps;
    }

    /**
     * @return string
     */
    public static function complete(): string
    {
        $request = explode('/',
            filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES));
        foreach ($request as $id => $fragment) {
            switch (true) {
                case empty($fragment):
                case str_contains($fragment, '.php'):
                    unset($request[$id]);
                    break;
                default:
            }
        }
        $request = implode('/', $request);

        return self::protocol().self::domain()."/$request";
    }
}
