<?php

namespace Myth\Api\Commands;

use Illuminate\Console\Command;

abstract class BaseCommand extends Command
{

    /**
     * @var string
     */
    protected $argumentName = '';

}