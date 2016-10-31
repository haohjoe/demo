<?php

class ParameterValidationException extends ErrorException
{

    public function __construct($message, $code = Errno::PARAMETER_VALIDATION_FAILED)
    {
        parent::__construct($message, $code);
    }
}
