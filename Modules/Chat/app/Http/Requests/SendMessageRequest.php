<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $conversation = $this->route('conversation');
        return $conversation && $this->user()->can('sendMessage', $conversation);
    }

    public function rules(): array
    {
        return [
            'message' => 'required_without:attachment|nullable|string|max:2000',
            'attachment' => 'required_without:message|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
