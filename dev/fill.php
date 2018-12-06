<?php

// Для заполнения БД данными

require 'vendor/autoload.php';

set_time_limit(30 * 60);

$pdo = require('db/pdo.php');

$sql = 'INSERT INTO site_feedback (name, email_local, email_domain, text) VALUES ';

for ($i = 0; $i < 5000; $i++) {
    $values = '';
    for ($j = 0; $j < 500; $j++) {
        $faker = Faker\Factory::create();
        $email = explode('@',$faker->email);

        $values .= "({$pdo->quote($faker->name)}, '$email[0]', '$email[1]', {$pdo->quote($faker->text())}),";
    }

    $stm = $pdo->exec(
        $sql.rtrim($values,',')
    );
}

