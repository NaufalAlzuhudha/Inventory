<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class storeTransaksiMasukRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pengirim' => 'required',
            'kontak' => 'required',
            'keterangan' => 'nullable|string',
            'items' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'pengirim.required' => 'Pengirim harus diisi',
            'kontak.required' => 'Kontak harus diisi',
            'items.required' => 'Item harus diisi',
            'items.min' => 'Minimal ada 1 item'
        ];
    }
}
