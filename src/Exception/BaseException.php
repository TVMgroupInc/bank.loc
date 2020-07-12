<?php

namespace App\Exception;

/**
 * Class BaseException
 * @package App\Exception
 */
class BaseException extends \Exception
{
    protected $message = '';

    /**
     * BaseException constructor.
     * @param $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->message . $message, $code, $previous);
    }
}