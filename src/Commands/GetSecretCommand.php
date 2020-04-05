<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Commands;

use Exception;
use Myth\Api\Exceptions\GetSecretException;
use Myth\Api\Facades\Api;

/**
 * Class GetSecretCommand
 * @package Myth\Api\Commands
 */
class GetSecretCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'myth:get-secret';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'get application secret for authentication';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws GetSecretException
     */
    public function handle()
    {
        try{
            echo "Application secret: ";
            $this->info(Api::secret());
        }
        catch(Exception $exception){
            throw new GetSecretException();
        }
    }
}
