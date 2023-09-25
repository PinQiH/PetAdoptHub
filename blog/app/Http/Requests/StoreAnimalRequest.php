<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalRequest extends FormRequest
{
    /**
     * 是否登入才能請求
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 請求的資料欄位規則
     *
     * @return array
     */
    public function rules()
    {
        // 把 app/Http/Controllers/AnimalController.php 中 store 方法裡，之前寫的驗證表單規則複製過來
        return [
            'type_id' => 'required',
            'name' => 'required|max:255',
            'birthday' => 'required|date',
            'area' => 'required|max:255',
            'fix' => 'required|boolean',
            'description' => 'nullable',
            'personality' => 'nullable'
        ];
    }
}
