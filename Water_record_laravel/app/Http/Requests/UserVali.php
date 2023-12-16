<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserVali extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'=>'required|email',
            'password'=>'required|max:20'
        ];
    }
    public function messages()
    {
        return [
            'email.required'=>'邮箱是必要的',
            'email.email'=>'邮箱格式错误',
            'password.required'=>'密码是必要的',
            'password.max'=>'密码长度不能超过20位'
        ];
    }
}
