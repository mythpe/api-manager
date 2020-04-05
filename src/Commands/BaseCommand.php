<?php

namespace Myth\Api\Commands;

use Illuminate\Console\Command;

/**
 * Class BaseCommand
 * @package Myth\Api\Commands
 */
abstract class BaseCommand extends Command
{

    /**
     * @var string
     */
    protected $argumentName = '';

}