<?php

namespace App\Exceptions;

use Exception;

class LostComposerJsonFileException extends Exception
{
    //
    protected $message = '仓库缺少 composer.json，不符合 PHP package 规范';
}
