<?php

namespace NaqlaSehia;

use NaqlaSehia\Http\Route;
use NaqlaSehia\Database\DB;
use NaqlaSehia\Http\Request;
use NaqlaSehia\Http\Response;
use NaqlaSehia\Support\Config;
use NaqlaSehia\Support\Session;
use NaqlaSehia\Database\Managers\MySQLManager;
use NaqlaSehia\Database\Managers\SQLiteManager;

class Application
{
    protected Route $route;
    protected Request $request;
    protected Response $response;
    protected DB $db;
    protected Config $config;
    protected Session $session;

    public function __construct()
    {
        $this->request = new Request;
        $this->response = new Response;
        $this->route = new Route($this->request, $this->response);
        $this->db = new DB($this->getDatabaseDriver());
        $this->config = new Config($this->loadConfigurations());
        $this->session = new Session;
    }

    protected function getDatabaseDriver()
    {
        return match(env('DB_DRIVER')) {
            'sqlite' => new SQLiteManager,
            'mysql' => new MySQLManager,
            default => new SQLiteManager
        };
    }

    protected function loadConfigurations()
    {
        $configPath = config_path();
        
        if (!is_dir($configPath)) {
            return [];
        }

        $configs = [];
        foreach (scandir($configPath) as $file) {
            if ($file == '.' || $file == '..' || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $configs[$filename] = require $configPath . $file;
        }

        return $configs;
    }

    public function run()
    {
        try {
            $this->db->init();
            $this->route->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode(500);
            throw $e;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        
        return null;
    }
}
