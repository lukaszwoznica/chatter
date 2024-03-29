<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = auth()->id();

        return [
            'text' => 'required|string|max:2000',
            'recipient_id' => "required|integer|not_in:$id|exists:users,id",
            'is_location' => 'boolean'
        ];
    }
}
