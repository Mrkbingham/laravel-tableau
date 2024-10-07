<?php

namespace InterWorks\Tableau;

use InterWorks\Tableau\Auth\TableauAuth;
use InterWorks\Tableau\Api\Workbooks;
use InterWorks\Tableau\Api\Views;
use InterWorks\Tableau\Api\Datasources;
use InterWorks\Tableau\Api\Users;
use InterWorks\Tableau\Api\Notifications;
use Illuminate\Support\Facades\Config;

class TableauAPI
{
    protected $auth;

    protected $workbooks;
    protected $views;
    protected $datasources;
    protected $users;
    protected $notifications;

    public function __construct()
    {
        // Initialize authentication
        $this->auth  = new TableauAuth();

        // Initialize API resource classes
        $this->workbooks = new Workbooks($this->auth);
        // TODO: Build additional API resource classes
        // $this->views = new Views($this->auth);
        // $this->datasources = new Datasources($this->auth);
        // $this->users = new Users($this->auth);
        // $this->notifications = new Notifications($this->auth);
    }

    /**
     * Get Auth methods
     *
     * @return TableauAuth
     */
    public function auth()
    {
        return $this->auth;
    }

    /**
     * Get Workbooks API
     */
    public function workbooks()
    {
        return $this->workbooks;
    }

    /**
     * Get Views API
     */
    public function views()
    {
        return $this->views;
    }

    /**
     * Get Datasources API
     */
    public function datasources()
    {
        return $this->datasources;
    }

    /**
     * Get Users API
     */
    public function users()
    {
        return $this->users;
    }

    /**
     * Get Notifications API
     */
    public function notifications()
    {
        return $this->notifications;
    }

    /**
     * Dynamically update the config and reinitialize
     */
    public static function reinitialize($config)
    {
        // Dynamically set configuration values (e.g., server_url, username, etc.)
        foreach ($config as $key => $value) {
            Config::set('tableau.' . $key, $value);
        }

        // Reinitialize a new instance of TableauApi with the updated config
        return new self();
    }
}
