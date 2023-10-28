<?php
use Faker\Generator as Faker;
use App\Admin;

$factory->define(Admin::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        // Định nghĩa thêm các trường dữ liệu khác
    ];
});
