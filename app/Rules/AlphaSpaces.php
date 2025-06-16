<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class AlphaSpaces implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if (!preg_match("/^[\pL\s]+$/u", $value)){
            $fail('The :attribute may only contain letters.');
        }
    }
}
