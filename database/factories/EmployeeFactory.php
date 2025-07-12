<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name'      => $this->faker->name(),
            'user_id'        => null, 
            'position_id'    => Position::factory(),
            'department_id'  => Department::factory(),
            'email'          => $this->faker->unique()->safeEmail(),
            'phone'          => $this->faker->phoneNumber(),
            'address'        => $this->faker->address(),
            'gender'         => $this->faker->randomElement(['male', 'female']),
            'status'         => $this->faker->randomElement(['active', 'inactive', 'terminated']),
            'birth_date'     => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'hire_date'      => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'salary'         => $this->faker->randomFloat(2, 3000, 20000),
            'national_id'    => $this->faker->unique()->numerify('##############'),
            'notes'          => $this->faker->optional()->sentence(),
        ];
    }
}
