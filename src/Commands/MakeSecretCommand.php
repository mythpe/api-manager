<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Commands;

use Illuminate\Console\ConfirmableTrait;
use Myth\Api\Facades\Api;

/**
 * Class MakeSecretCommand
 * @package Myth\Api\Commands
 */
class MakeSecretCommand extends BaseCommand
{

    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'myth:make-secret';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'make new application secret to authentication';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if(!$this->confirmToProceed()) return;

        $secret = Api::makeSecret();
        $this->comment("Application secret created Successfully");
        echo "Application secret: ";
        $this->info($secret);
    }
}
