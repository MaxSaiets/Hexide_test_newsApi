<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\NewsBlockType;

class UpdateNewsBlockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(NewsBlockType::values())],
            'text_content' => 'required_unless:type,image|string|nullable',
            'image_path' => [Rule::requiredIf(function(){
               $type = NewsBlockType::tryFrom($this->input('type'));
               return $type && $type->hasImage();
            }),'max:2048','image','mimes:jpeg,png,jpg,gif'],
            'position' => 'integer',
        ];
    }
}
