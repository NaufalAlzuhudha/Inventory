<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KartuStok;
use App\Http\Resources\KartuStokResource;

class KartuStokController extends Controller
{
    public function kartuStok($nomor_sku)
    {
        $query = KartuStok::where('nomor_sku', $nomor_sku)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return KartuStokResource::collection($query);
    }
}
