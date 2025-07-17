<?php

declare(strict_types=1);

namespace App\Application\Presenters\Html\ViewPages;

abstract readonly class ViewPage
{
    abstract public function isEmpty(): bool;
}
