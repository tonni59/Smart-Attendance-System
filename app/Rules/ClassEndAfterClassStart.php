<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ClassEndAfterClassStart implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $class_start;

    public function __construct($class_start)
    {
        $this->class_start = $class_start;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $start = \DateTime::createFromFormat('H:i', $this->class_start);
        $end = \DateTime::createFromFormat('H:i', $value);
        if ($start === false || $end === false) {
            return false;
        }
        $interval = $start->diff($end);
        if ($interval->invert) {
            return false;
        }
        $hours = $interval->format('%h');

        return $hours >= 1;
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field must be at least 1 hour after the class start field.';
    }
}
