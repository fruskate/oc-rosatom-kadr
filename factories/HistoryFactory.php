<?php

use Carbon\Carbon;
use Frukt\Kadr\Models\History;
use Faker\Generator as Faker;

$factory->define(History::class, function (Faker $faker) {
    $array = [1,2,3,4,7,8,9];
    return [
        'specialist_id' => rand(3461, 8460),
        'condition_id' => $array[array_rand($array, 1)],
        'amount' => 1,
    ];


});
