<?php

namespace App\Http\Requests\Character;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCharacterAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attribute' => [
                'strength' => ['required','integer'],
                'agility' => ['required','integer'],
                'stamina' => ['required','integer'],
                'intelligence' => ['required','integer'],
            ]
        ];
    }
}
