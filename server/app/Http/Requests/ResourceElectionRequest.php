<?php

namespace App\Http\Requests;

use App\Model\Permission;
use App\Model\User;
use Dingo\Api\Auth\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ResourceElectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var Auth $apiAuth */
        $apiAuth = app(Auth::class);
        /** @var User $user */
        $user = $apiAuth->user();
        return $user->can(Permission::NAME_ISSUE_RESOURCE_ELECT);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'issue_id' => 'required|exists:issue',
            'user_id' => 'required|exists:user',
            'resource_id' => 'required|exists:resource',
        ];
    }
}
