<?php


namespace App\Service;


use Exception;

class TokenGenerator
{
    /**
     * Generate a token depending on size
     * @param int $length
     * @return string
     * @throws Exception
     */
    final public function generate(int $length = 10): string
    {
        return bin2hex(random_bytes($length));
    }
}