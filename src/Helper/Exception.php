<?php
declare(strict_types=1);

namespace PhpMultiCurl\Helper;

use Exception as phpException;

class Exception extends phpException
{
    public function __construct($message)
    {
        return parent::__construct('[PhpMultiCurl] '.$message);
    }
}
