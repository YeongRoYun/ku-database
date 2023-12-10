<?php

namespace app\exception;

// Error -> Exception으로 변환

function exceptions_error_handler($severity, $message, $filename, $lineno)
{
    throw new \ErrorException($message, 0, $severity, $filename, $lineno);
}

set_error_handler('\app\exception\exceptions_error_handler');
