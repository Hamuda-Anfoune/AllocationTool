<?php

namespace App\Policies\Concerns;

use Illuminate\Auth\Access\Response;

trait AuthorizesWithMessage
{
    private function allowIf(bool $condition, string $denyMessage): Response
    {
        return $condition ? Response::allow() : Response::deny($denyMessage);
    }
}
