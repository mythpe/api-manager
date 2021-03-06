<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Class MakeClientTransformerCommand
 * @package Myth\Api\Commands
 */
class MakeClientTransformerCommand extends BaseCommand
{

    /**
     * The console command signature.
     * @var string
     */
    protected $signature = 'myth:make-client-transformer {name : The name of the transformer}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new client api transformer of model file';

    /** @var Filesystem */
    protected $fs;

    /**
     * Create a new command instance.
     * @param \Illuminate\Filesystem\Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        parent::__construct();
        $this->fs = $fs;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $fs = $this->fs;
        $name = trim($this->input->getArgument('name'));
        $name = preg_replace('/[\s\\\\]+/', '/', $name);
        $name = ucwords(Str::after(Str::camel($name), '/'));
        $file = "{$name}.php";
        $path = $this->path($file);
        if($fs->exists($path)){
            $this->error("File Exist!!");
            return;
        }
        $stub = $this->populateStub($this->getStub(), [
            'CLASS_NAME' => $name,
        ]);
        $this->fs->put($path, $stub);
        $this->info("File Created !! ");
        $this->comment($path);
    }

    /**
     * @param $stub
     * @param array $replace
     * @return string|string[]
     */
    public function populateStub($stub, $replace = [])
    {
        $v = array_map(function ($v) { return "{".trim($v, "{}")."}"; }, array_keys($replace));
        return $stub = str_replace($v, $replace, $stub);
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    function getStub()
    {
        return $this->fs->get($this->stubPath('client-transformer'));
    }

    /**
     * @param null $stub
     * @return string
     */
    public function stubPath($stub = null)
    {
        return __DIR__.'/../stubs'.(!is_null($stub) ? "/".trim($stub, '/').'.stub' : '');
    }

    /**
     * @param string $path
     * @return string
     */
    protected function path($path = '')
    {
        return app_path($path);
    }
}
