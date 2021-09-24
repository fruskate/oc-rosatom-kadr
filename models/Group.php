<?php namespace Frukt\Kadr\Models;

use Model;

/**
 * Model
 */
class Group extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\SimpleTree;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    public $jsonable = ['options'];

    protected $slugs = ['code' => 'name'];

    protected $fillable = ['name', 'parent_id'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'frukt_kadr_groups';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsToMany = [
        'specialists' => [
            \Frukt\Kadr\Models\Specialist::class,
            'table' => 'frukt_kadr_specialist_group'
        ]
    ];

    public function beforeCreate()
    {
        if (!$this->color) {
            $this->color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
    }
}
