<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Build;
use App\Modpack;
use App\Privacy;

$factory->define(Build::class, function (Faker\Generator $faker) {
    return [
        'version' => '1.0.0',
        'game_version' => '1.0.0',
        'privacy' => Privacy::PUBLIC,
        'modpack_id' => function () {
            return factory(Modpack::class)->create()->id;
        },
    ];
});

$factory->state(Build::class, 'public', function () {
    return [
        'privacy' => Privacy::PUBLIC,
    ];
});

$factory->state(Build::class, 'unlisted', function () {
    return [
        'privacy' => Privacy::UNLISTED,
    ];
});

$factory->state(Build::class, 'private', function () {
    return [
        'privacy' => Privacy::PRIVATE,
    ];
});
