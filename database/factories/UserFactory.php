<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'middleinitial' => ucfirst($this->faker->randomLetter),
            'email' => $this->faker->unique()->safeEmail,
            'student_no' => $this->faker->unique()->regexify('02000[0-9]{6}'),
            'section' => $this->faker->randomElement(['ICT 11-A', 'ICT 12-A', 'GAS 11-A', 'GAS 12-A', 'ABM 11-A', 'ABM 12-A', 'HUMSS 11-A', 'HUMSS 12-A', 'STEM 11-A', 'STEM 12-A', 'ICT 11-B', 'ICT 12-B', 'GAS 11-B', 'GAS 12-B', 'ABM 11-B', 'ABM 12-B', 'HUMSS 11-B', 'HUMSS 12-B', 'STEM 11-B', 'STEM 12-B', 'SPORT 11-A', 'SPORT 12-A', 'SPORT 11-B', 'SPORT 12-B']),
            'role' => 'student',
            'token' => Str::random(20),
            'username' => $this->faker->unique()->userName,
            'password' => bcrypt('123'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'approved' => false,
            'school_year_id' => '2022-2023',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
