<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

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