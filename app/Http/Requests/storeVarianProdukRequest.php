<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class storeVarianProdukRequest extends FormRequest
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
            'produk_id' => 'required|exists:produks,id',
            'nama_varian' => 'required',
            'gambar_varian' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'harga_varian' => 'required|numeric|min:0',
            'stok_varian' => 'required|numeric|min:0',
        ];
    }
}
