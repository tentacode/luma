<?php declare(strict_types=1);

namespace App\Luma\Exception;

use RuntimeException;

final class HueException extends RuntimeException
{
    const ERROR_BUTTON_NOT_PRESSED = 101;
}
