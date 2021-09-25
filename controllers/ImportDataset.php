<?php namespace Frukt\Kadr\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Frukt\Kadr\Imports\UsersImport;
use Frukt\Kadr\Imports\MentorImport;
use Excel;

/**
 * Import Dataset Backend Controller
 */
class ImportDataset extends Controller
{
    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Frukt.Kadr', 'main-menu-item2', 'side-menu-item4');
    }

    public function index()
    {

    }

    public function onLoadXLSFile()
    {
        Excel::import(new UsersImport, \Input::file('file'));

        return [
            '#answer' => 'Данные успешно загружены. Хорошей работы, о мой повелитель!'
        ];
    }

    public function onLoadXLSFileWithMentors()
    {
        Excel::import(new MentorImport, \Input::file('file'));

        return [
            '#answerMentors' => 'Данные по наставникам успешно загружены, о мой повелитель!'
        ];
    }

}
