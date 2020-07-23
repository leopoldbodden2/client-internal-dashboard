<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\MailgunForms::class, function (Faker $faker) {
    $date = $faker->dateTimeThisYear();
    $email = $faker->unique()->safeEmail;
    $mail_message = "Contact Form\n";
    $mail_message .= "Name: " . $faker->name . "\n";
    $mail_message .= "Phone: " . $faker->phoneNumber . "\n";
    $mail_message .= "Email: " . $email. "\n";
    $form_checkboxes    = implode( "\n", $faker->words(3) );
    $mail_message       .= "Interested In: \n" . $form_checkboxes . "\n";
    $mail_message .= "\nMessage: \n" . $faker->text(250) . "\n";

    return [
        'domain' => 'mg.example.com',
        'email' => $email,
        'message' => $mail_message,
        'created_at' => $date,
        'updated_at' => $date
    ];
});
