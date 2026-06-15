<?php

namespace App\Http\Requests\Concerns;

trait SanitizesInput
{
    protected function trimStrings(array $fields, array $nullableFields = []): void
    {
        $merged = [];

        foreach ($fields as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);

            if (is_string($value)) {
                $value = trim($value);
            }

            if (in_array($field, $nullableFields, true) && ($value === '' || $value === null)) {
                $value = null;
            }

            $merged[$field] = $value;
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }
}
