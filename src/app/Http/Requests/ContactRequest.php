<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'integer', 'min:1', 'max:3'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'tel' => ['required', 'numeric', 'digits_between:2,5'],
            'tel_middle' => ['required', 'numeric', 'digits_between:1,5'],
            'tel_bottom' => ['required', 'numeric', 'digits_between:3,5'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['max:255'],
            'detail' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(){
        return [
            
            'first_name.required' => '名を入力してください',
            'first_name.string' => '名前を文字列で入力してください',
            'first_name.max' => '名前を255文字以下で入力してください',
            'last_name.required' => '姓を入力してください',
            'last_name.string' => '名前を文字列で入力してください',
            'last_name.max' => '名前を255文字以下で入力してください',
            'gender.required' => '性別を選択してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.string' => 'メールアドレスを文字列で入力してください',
            'email.email' => '有効なメールアドレス形式を入力してください',
            'email.max' => 'メールアドレスを255文字以下で入力してください',
            'tel.required' => '電話番号(1)を入力してください',
            'tel.numeric' => '電話番号(1)を数値で入力してください',
            'tel.digits_between' => '電話番号(1)を2桁から5桁の間で入力してください',
            'tel_middle.required' => '電話番号(2)を入力してください',
            'tel_middle.numeric' => '電話番号(2)を数値で入力してください',
            'tel_middle.digits_between' => '電話番号(2)を1桁から5桁の間で入力してください',
            'tel_bottom.required' => '電話番号(3)を入力してください',
            'tel_bottom.numeric' => '電話番号(3)を数値で入力してください',
            'tel_bottom.digits_between' => '電話番号(3)を3桁から5桁の間で入力してください',
            'address.required' => '住所を入力してください',
            'address.string' => '住所を文字列で入力してください',
            'address.max' => '住所を255文字以下で入力してください',
            'detail.required' => 'お問い合わせ内容を入力してください',
            'detail.max' => 'お問合せ内容は120文字以内で入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'category_id.integer' => 'お問い合わせの種類を選択してください',
            'category_id.min' => 'お問い合わせの種類を選択してください'
        ];
    }
}
