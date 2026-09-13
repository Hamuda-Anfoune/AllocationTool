<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when the allocation algorithm reaches a module that has not submitted preferences for the current academic year.
 */
class ModuleHasNoPreferencesException extends RuntimeException
{
    public function __construct(public readonly string $moduleId)
    {
        parent::__construct("Module {$moduleId} has not submitted preferences for the current academic year.");
    }
}
