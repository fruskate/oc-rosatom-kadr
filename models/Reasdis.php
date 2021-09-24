<?php namespace Frukt\Kadr\Models;

use Model;

/**
 * Model
 */
class Reasdis extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'frukt_kadr_reasdises';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $hasMany = [
        'specialists' => \Frukt\Kadr\Models\Specialist::class
    ];
}
