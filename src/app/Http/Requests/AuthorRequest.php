<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// use Egulias\EmailValidator\Validation\RFCValidation;
// use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;


class AuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

      return [
        // 'name' => ['required', 'string', 'max:255'],
        // 'email' => ['required', 'string', 'email', 'max:255'],
        // 'password' => ['required', 'string', 'max:255'],

        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email:strict', 'max:255', 'unique:users'],
        'password' => ['required', 'string']
      ];
    }

    public function messages()
    {
      return [
        'name.required' => 'お名前を入力してください',
        'email.required' => 'メールアドレスを入力してください',
        'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
        'password.required' => 'パスワードを入力してください',
      ];
    }
}
