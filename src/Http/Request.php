<?php

namespace NaqlaSehia\Http;

use NaqlaSehia\Support\Arr;
use NaqlaSehia\Support\Str;

class Request
{
    public function path()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        return str_contains($path, '?') ? explode('?', $path)[0] : $path;
    }

    public function method()
    {
        return Str::lower($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isMethod($method)
    {
        return $this->method() === Str::lower($method);
    }

    public function all()
    {
        return array_merge($_GET ?? [], $_POST ?? []);
    }

    public function only($keys)
    {
        return Arr::only($this->all(), $keys);
    }

    public function except($keys)
    {
        return Arr::except($this->all(), $keys);
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->all(), $key, $default);
    }

    public function has($key)
    {
        return Arr::has($this->all(), [$key]);
    }

    public function input($key, $default = null)
    {
        return $this->get($key, $default);
    }
}
