<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class InputSanitizer
{
    protected $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize($input)
    {
        return $this->purifier->purify($input);
    }
}
