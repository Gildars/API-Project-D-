<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class StoreStatUserRequest
 * @package App\Http\Requests\User
 */
class ItemRequest extends FormRequest
{
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
        return [
            'item' => 'uuid'
        ];
    }

    public function all($keys = null)
    {
        $request = parent::all();
        $request['item'] = $this->route('item');
        return $request;
    }
}
