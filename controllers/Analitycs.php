<?php namespace Frukt\Kadr\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Carbon\Carbon;
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
        try {
            list($started, $ended) = explode('|', post('dates'));

            $started = Carbon::parse($started)->toDateTimeString();
            $ended = Carbon::parse($ended)->toDateTimeString();
        } catch (\Exception $exception) {
            throw new \ValidationException(['dates' => 'Пожалуйста, выберите даты!']);
        }


        $groups = post('groups');

        $conditions = Condition::whereIn('id', post('conditions'))->get();

        $conditionsFinalList = array();

        foreach ($conditions as $condition) {
            $conditionsFinalList[] = ['name' => $condition->name];
        }

        $groups = Group::whereIn('id', post('groups'))->get();

        $arrayOfGroups = array();

        foreach ($groups as $group) {
                list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");
                $arrayOfGroups[$group->id] = [
                    'name' => $group->name,
                    'color' => [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                    ],
                    'count' => array()
                ];
            foreach ($conditions as $condition) {
                $arrayOfGroups[$group->id]['count'][] = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereHas('histories', function ($query) use ($condition) {
                            $query->where('condition_id', $condition->id);
                        })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->count();
            }

            if (post('salary')) {
                if (post('salary')) {
                    $conditionsFinalList['salary'] = ['name' => 'Средняя зарплата'];
                }


                $salary = Specialist::whereHas('groups', function ($query) use ($group) {
                    $query->where('id', $group->id);
                })
                    ->whereIn('reasdis_id', post('reasdises'))
                    ->where('is_ended', true)
                    ->where('ended_at', '>=', $started)
                    ->where('ended_at', '<=', $ended)
                    ->sum('salary');
                $charsCount = Specialist::whereHas('groups', function ($query) use ($group) {
                    $query->where('id', $group->id);
                })
                    ->whereIn('reasdis_id', post('reasdises'))
                    ->where('is_ended', true)
                    ->where('ended_at', '>=', $started)
                    ->where('ended_at', '<=', $ended)
                    ->count();
                $arrayOfGroups[$group->id]['count'][] = ($charsCount > 0)? round($salary / $charsCount): 0;
            }
        }

        trace_log($arrayOfGroups);

        return [
            '#answer' => \Twig::parse($this->makePartial('make_correlation'), [
                'conditions' => $conditionsFinalList,
                'groups'    => $arrayOfGroups
            ]),
        ];
    }

}
