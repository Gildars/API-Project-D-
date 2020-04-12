<?php

namespace App\Http\Requests\Conversation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

/**
 * Class StoreConversationMessageRequest
 *
 * @package App\Http\Requests\Converstion
 */
class DeleteConversationRequest extends FormRequest
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
        return [
            'id' => "required|integer|exists:conversations",
        ];
    }

    public function all($keys = null)
    {
        // Include the next line if you need form data, too.
        $request = parent::all();
        $request['id'] = $this->route('id');
        return $request;
}
}
