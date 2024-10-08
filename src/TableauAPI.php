<?php

namespace InterWorks\Tableau;

use Illuminate\Support\Facades\Config;
use InterWorks\Tableau\Api\Datasources;
use InterWorks\Tableau\Api\Notifications;
use InterWorks\Tableau\Api\Users;
use InterWorks\Tableau\Api\Views;
use InterWorks\Tableau\Api\Workbooks;
use InterWorks\Tableau\Auth\TableauAuth;
use InterWorks\Tableau\Enums\AuthType;

class TableauAPI
{
    /** @var TableauAuth */
    protected $auth;

    // API resource classes
    /** @var Datasources API resource class: Datasources */
    protected $datasources;
    /** @var Notifications API resource class: Notifications */
    protected $notifications;
    /** @var Users API resource class: Users */
    protected $users;
    /** @var Views API resource class: Views */
    protected $views;
    /** @var Workbooks API resource class: Workbooks */
    protected $workbooks;

    /**
     * TableauAPI constructor
     *
     * @param AuthType $authType The type of authentication to use (e.g., 'pat', 'username').
     *
     * @return void
     */
    public function __construct(AuthType $authType = AuthType::PAT)
    {
        // Initialize authentication
        $this->auth  = new TableauAuth($authType);

        // Initialize API resource classes
        $this->workbooks = new Workbooks($this->auth);

        // TODO: Build Views API resource class
        // $this->views = new Views($this->auth);
        // TODO: Build Datasources API resource class
        // $this->datasources = new Datasources($this->auth);
        // TODO: Build Users API resource class
        // $this->users = new Users($this->auth);
        // TODO: Build Notifications API resource class
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
     *
     * @return Workbooks
     */
    public function workbooks()
    {
        return $this->workbooks;
    }

    // TODO: Implement the following API resource classes
    // /**
    //  * Get Views API
    //  */
    // public function views()
    // {
    //     return $this->views;
    // }

    // TODO: Implement the following API resource classes
    // /**
    //  * Get Datasources API
    //  */
    // public function datasources()
    // {
    //     return $this->datasources;
    // }

    // TODO: Implement the following API resource classes
    // /**
    //  * Get Users API
    //  */
    // public function users()
    // {
    //     return $this->users;
    // }

    // TODO: Implement the following API resource classes
    // /**
    //  * Get Notifications API
    //  */
    // public function notifications()
    // {
    //     return $this->notifications;
    // }
}
