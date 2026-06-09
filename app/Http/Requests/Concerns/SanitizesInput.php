<?php

namespace App\Http\Requests\Concerns;

trait SanitizesInput
{
    protected function trimStrings(array $fields, array $nullableFields = []): void
    {
        $merged = [];

        foreach ($fields as $field) {
            if (! $this->has($field) || ! is_string($this->input($field))) {
                continue;
            }

            $value = trim($this->input($field));

            if (in_array($field, $nullableFields, true) && $value === '') {
                $value = null;
            }

            $merged[$field] = $value;
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }
}
