<?php

namespace App\Http\Requests\Conversation;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

/**
 * Class StoreConversationMessageRequest
 *
 * @package App\Http\Requests\Converstion
 */
class StoreConversationMessageRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $idUser = Auth::user()->id;
        return [
            'message' => 'required|string',
            'id' => "required|integer|not_in:{$idUser}",
            'offset' => 'numeric'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
          'id.not_in' => trans('validation.custom.message.id')
        ];
    }

}
