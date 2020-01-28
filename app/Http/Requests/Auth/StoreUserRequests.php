<?php

namespace App\Http\Requests\Auth;

use App\Models\CharacterClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreUserRequests extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guest();
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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|max:28',
            'name' => 'required|alpha|min:2|max:12|unique:users',
            'class' => [
                'required',
                Rule::in($classes),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'password' => trans('attributes.password'),
            'token' => trans('attributes.token'),
        ];
    }
}
