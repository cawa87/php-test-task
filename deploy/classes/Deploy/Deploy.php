<?php
namespace Deploy;

class Deploy
{

    protected $config;

    private $cache = false;

    private $curuser;

    public function __construct($config)
    {
        $this->config = $config;

        if ($this->config->cache) {
            $this->cache = new Memcache();
            $this->cache->connect('127.0.0.1', 11211) or die("cache - could not connect");
        }
    }

    public function run()
    {
        $this->lock();

        if ($this->update()) {
            $this->swith();
        }

        #$this->unlock();
    }

    protected function getMainDir()
    {
        return $this->config->path->dir ?? false;
    }

    protected function getStagingPath()
    {
        return $this->config->path->staging ?? false;
    }

    protected function getProductionPath()
    {
        return $this->config->path->production ?? false;
    }

    protected function swith()
    {
        try {
            $work_from = readlink($this->getStagingPath());
            $work_to = $this->getProductionPath();
            $tech_from = readlink($this->getProductionPath());
            $tech_to = $this->getStagingPath();
            $cmd = "cd {$this->getMainDir()} && ( /bin/ln -nfs {$work_from} {$work_to} && echo ok 2>&1 ; /bin/ln -nfs {$tech_from} {$tech_to} && echo ok 2>&1 )";
            system($cmd, $return);
            openlog('deploylog', LOG_ODELAY, LOG_LOCAL1);
            syslog(LOG_NOTICE, 'details: '. $return);
            closelog();
        } catch (\Exception $e) {
            return false;
        }

        return;
    }

    protected function update()
    {
        try {
            $cmd = sprintf("cd {$this->getStagingPath()} && git pull %s master", $this->config->project_url);
            system($cmd, $return);
            openlog('deploylog', LOG_ODELAY, LOG_LOCAL1);
            syslog(LOG_NOTICE, 'details: '. $return);
            closelog();
            /* @TODO проверка ответа */
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    protected function lock()
    {
        $time = date("d.m.Y H:i:s", time());

        if ($this->cache) {
            if ($this->cache->get($this->config->cache_key)) {
                throw new \Exception('Deploy already runned or something go wrong');
            }

            $this->cache->set($this->config->cache_key, $time, $this->config->deploy_timeout);
        } else {
            if ($this->isLocked()) {
                throw new \Exception('Deploy already runned or something go wrong');
            }
            file_put_contents($this->config->lockfile, $time);
        }
        echo "Deploy locked at {$time} \n";

        return;
    }

    protected function isLocked()
    {
        return file_exists($this->config->lockfile);
    }

    protected function unlock()
    {
        if ($this->cache) {
            $this->cache->delete($this->config->cache_key);
        } else {
            unlink($this->config->lockfile);
        }

        echo "Deploy unlocked \n";
        return ;
    }

    public function t1()
    {
        echo "ddd";
    }
}