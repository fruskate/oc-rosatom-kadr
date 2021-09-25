<?php

use Carbon\Carbon;
use Frukt\Kadr\Models\Specialist;
use Faker\Generator as Faker;

$factory->define(Specialist::class, function (Faker $faker) {

    $ran = array(1, 0, 0, 0, 0, 0, 0);
    $isEnded = $ran[array_rand($ran, 1)];
    $dateStart = Carbon::today()->subDays(rand(1, 267));

    return [
        'fio' => Str::random(10),
        'borned_at' => Carbon::today()->subDays(rand(7400, 25550)),
        'started_at' => $dateStart,
        'ended_at' => ($isEnded)? Carbon::today()->subDays(rand(1, Carbon::now()->diffInDays($dateStart) - 10)) : null,
        'is_ended' => $isEnded,
        'reasdis_id' => ($isEnded)? $ran[array_rand([1, 1, 1, 1, 2], 1)]: null,
        'salary' => rand(25000, 60000),
        'sex_id' => rand(1,2), //$this->randomSex(),
        'position_id' => rand(1,54), //$this->randomPosition(),
        'family_id' => rand(1,5), //$this->randomFamily(),
        'childs' => rand(0,2), //$this->randomChilds(),
    ];


});
