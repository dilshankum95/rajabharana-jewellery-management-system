<?php

namespace App\Http\Requests\Technician;

use App\Http\Requests\Concerns\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondToTaskRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        $order = $this->route('order');

        return $this->user()?->isTechnician()
            && $order->technicianCanRespondToTask($this->user());
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['accept', 'reject'])],
        ];
    }
}
