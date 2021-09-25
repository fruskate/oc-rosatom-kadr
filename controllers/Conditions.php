<?php namespace Frukt\Kadr\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Conditions extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'helpers'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Frukt.Kadr', 'main-menu-item2', 'side-menu-item3');
    }
}