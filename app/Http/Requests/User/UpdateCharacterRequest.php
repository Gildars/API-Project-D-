<?php

namespace App\Http\Requests\User;

use App\Models\CharacterClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCharacterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $classes = CharacterClass::all('id')->getQueueableIds();

        return [
            'name' => 'required|alpha|min:2|max:12|unique:users',
            'class' => [
                'required',
                Rule::in($classes),
            ],
        ];
    }
}
