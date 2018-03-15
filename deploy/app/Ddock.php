<?php

namespace App;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class Ddock
{
    const CONFIG_FILE = 'config.yml';

    const CONFIG_PATH = __DIR__ . '/config';

    protected $input;

    protected $config;

    protected $factory;

    protected $lock;

    protected $tasks;

    protected $queue;

    /**
     * Ddock constructor.
     *
     * @param $input
     */
    public function __construct($input)
    {
        $this->input = new ArgvInput;
        $this->config = $this->loadConfig();

        $store = new FlockStore(sys_get_temp_dir());
        $this->factory = new Factory($store);
    }

    public function start($action)
    {
        $this->lock = $this->factory->createLock('project-deployment');

        if ($this->lock->acquire()) {

            $this->prepareDeploy();

            echo 'start ' . $action . PHP_EOL;
            $result = $this->runTasks($this->tasks[$action]) ? 'success' : 'failure';

            if (isset($this->queue[$action][$result])) {
                foreach ($this->queue[$action][$result] as $next) {
                    $this->start($next);
                }
            }

            $this->lock->release();
        }
    }

    public function config()
    {
        return $this->config;
    }

    public function task($key, $command)
    {
        [$action, $descriptor] = explode(':', $key);
        $this->tasks[$action][$descriptor] = $command;

        return $this;
    }

    public function after($action)
    {
        $this->queue[$action] = [];

        return $this;
    }

    public function success($next)
    {
        end($this->queue);
        $this->queue[key($this->queue)]['success'][] = $next;

        return $this;
    }

    public function failure($next)
    {
        end($this->queue);
        $this->queue[key($this->queue)]['failure'][] = $next;

        return $this;
    }

    /**
     * @param string|null $resource
     *
     * @return array|mixed
     */
    private function loadConfig($resource = null)
    {
        $resource = $resource ?: self::CONFIG_FILE;
        $config = Yaml::parseFile(self::CONFIG_PATH . '/' . $resource);

        if (isset($config['imports'])) {
            foreach ($config['imports'] as $import) {
                $config = array_merge_recursive($config, $this->loadConfig($import['resource']));
                unset($config['imports']);
            }
        }

        return $config;
    }

    private function prepareDeploy()
    {
        /**
         * Prepare pre-deploy commands
         */
        $tasks[] = 'cd ' . $this->config['params']['dir'];

        $gitTag = $this->input->getParameterOption('--tag');
        $gitOptions = $gitTag ? 'tags/' . $gitTag : $this->config['git']['branch'] ?? '';

        $tasks[] = 'git checkout ' . $gitOptions;

        array_unshift($this->tasks['deploy'], $tasks);

        /**
         * Prepare case on success
         * Create soft-link for project to server
         */
        $this->queue['deploy']['success'][] = 'ln -s '
                                            . $this->config['params']['dir'] . ' '
                                            . $this->config['server']['path'];

        /**
         * Prepare case on failure
         */
        $process = new Process('git tag');
        $process->run('git tag');

        $this->queue['deploy']['failure'][] = 'git checkout ' . $process->getOutput();

    }

    private function runTasks($tasks)
    {
        foreach ($tasks as $descriptor => $command) {
            try {
                echo 'run ' . $command . PHP_EOL;
                $this->runProcess($command);
            } catch (ProcessFailedException $exception) {
                return false;
            }
        }

        return true;
    }

    private function runProcess($command)
    {
        $process = new Process($command);
        $code = $process->mustRun();
        echo $process->getOutput();

        return $code;
    }
}