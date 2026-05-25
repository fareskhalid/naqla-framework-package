<?php

namespace NaqlaSehia\Http;

class Response
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_ERROR = 500;

    public function setStatusCode(int $code)
    {
        http_response_code($code);
        return $this;
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
        return $this;
    }

    public function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return $this->redirect($referer);
    }

    public function json($data, int $status = 200)
    {
        $this->setStatusCode($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        return $this;
    }

    public function setHeader($name, $value)
    {
        header($name . ': ' . $value);
        return $this;
    }
}
