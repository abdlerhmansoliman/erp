<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'payment_id' => 'required|exists:payments,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_id.required' => 'معرّف الدفع مطلوب.',
            'payment_id.exists' => 'الـ Payment غير موجود.',
            'payment_method_id.required' => 'طريقة الدفع مطلوبة.',
            'payment_method_id.exists' => 'طريقة الدفع غير موجودة.',
            'amount.required' => 'المبلغ مطلوب.',
            'amount.numeric' => 'المبلغ لازم يكون رقم.',
            'amount.min' => 'المبلغ لازم يكون أكبر من صفر.',
        ];
    }
}
