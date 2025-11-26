<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class MinimumAge implements Rule
{
    protected $minAge;

    /**
     * Create a new rule instance.
     *
     * @param int $minAge
     */
    public function __construct($minAge = 18)
    {
        $this->minAge = $minAge;
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
        // Si el valor es null o vacío, permitir (para campos nullable)
        // La validación 'required' se encargará de campos obligatorios
        if (!$value || $value === null || $value === '') {
            return true; // Permitir null/empty, la validación 'required' se encargará si es necesario
        }

        try {
            $birthday = Carbon::parse($value);
            $age = $birthday->age;
            
            return $age >= $this->minAge;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "El usuario debe tener al menos {$this->minAge} años de edad.";
    }
}

