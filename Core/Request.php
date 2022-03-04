<?php

namespace Core;

class Request
{
    public function __construct()
    {
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }
}