<?php namespace Frukt\Kadr\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Frukt\Kadr\Models\Condition;
use Frukt\Kadr\Models\Group;
use Frukt\Kadr\Models\Reasdis;
use Frukt\Kadr\Models\Specialist;

/**
 * Analitycs Backend Controller
 */
class Analitycs extends Controller
{

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Frukt.Kadr', 'main-menu-item', 'side-menu-item2');
    }

    public function index()
    {

    }

    public function correlation()
    {
        $this->addJs('/plugins/frukt/kadr/assets/js/datepicker.min.js');
        $this->addJs('/plugins/frukt/kadr/assets/js/correlation.js');
        $this->addJs('/plugins/frukt/kadr/assets/js/chart.min.js', '1.0.0');
        $this->addCss('/plugins/frukt/kadr/assets/css/datepicker.min.css');

        $this->vars['reasdises'] = Reasdis::all();
        $this->vars['groups'] = Group::all();
        $this->vars['conditions'] = Condition::all();
    }

    public function onMakeCorrelation()
    {
        trace_log(post());

        $groups = post('groups');

        $conditions = Condition::whereIn('id', post('conditions'))->get();

        $arrayOfWorkers = array();
        $arrayOfFuckers = array();

        foreach ($conditions as $condition) {
            $arrayOfWorkers[] = Specialist::whereHas('groups', function ($query) use ($groups) {
                $query->whereIn('id', $groups);
            })
                ->whereHas('histories', function ($query) use ($condition) {
                    $query->where('condition_id', $condition->id);
                })
                ->where('is_ended', false)
                ->count();
            $arrayOfFuckers[] = Specialist::whereHas('groups', function ($query) use ($groups) {
                $query->whereIn('id', $groups);
            })
                ->whereHas('histories', function ($query) use ($condition) {
                    $query->where('condition_id', $condition->id);
                })
                ->whereIn('reasdis_id', post('reasdises'))
                ->where('is_ended', true)
                ->count();
        }

        return [
            '#answer' => \Twig::parse($this->makePartial('make_correlation'), [
                'conditions' => $conditions,
                'workers'    => $arrayOfWorkers,
                'fuckers'    => $arrayOfFuckers
            ]),
        ];
    }

}
