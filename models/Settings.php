<?php namespace Frukt\Kadr\Models;

use Model;

/**
 * Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'frukt_kadr_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public function getListConditions()
    {
        $conditions = Condition::select('id', 'name')->get()->toArray('id', 'name');

        $returnArray = array();

        foreach ($conditions as $condition) {
            $returnArray[$condition['id']] = $condition['name'];
        }

        return $returnArray;
    }
}
