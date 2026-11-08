<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public $links;
    public function __construct()
    {
        $this->links = [
            [
                'label' => 'Dashboard Analitik',
                'icon' => 'fas fa-home',
                'route' => 'dashboard',
                'is_active' => request()->routeIs('dashboard'),
                'is_dropdown' => false,

            ],
            [
                'label' => 'Master Data',
                'icon' => 'fas fa-home',
                'route' => '#',
                'is_active' => request()->routeIs('master-data.*'),
                'is_dropdown' => true,
                'items' => [
                    [
                        'label' => 'Kategori Produk',
                        'route' => 'master-data.kategori-produk.index'
                    ],
                    [
                        'label' => 'Data Produk',
                        'route' => 'master-data.produk.index'
                    ],
                    [
                        'label' => 'Stok Barang',
                        'route' => 'master-data.stok-barang.index'
                    ]
                ]

            ],
            [
                'label' => 'Transaksi Masuk',
                'icon' => 'fas fa-truck-loading',
                'route' => '#',
                'is_active' => request()->routeIs('transaksi-masuk.*'),
                'is_dropdown' => true,
                'items' => [
                    [
                        'label' => 'Transaksi Baru',
                        'route' => 'transaksi-masuk.create'
                    ],
                    [
                        'label' => 'Data Transaksi',
                        'route' => 'transaksi-masuk.index'
                    ]
                ]

            ],
            [
                'label' => 'Laporan Kenaikan Harga',
                'icon' => 'fas fa-level-up-alt-line',
                'route' => 'laporan-kenaikan-harga.index',
                'is_active' => request()->routeIs('laporan-kenaikan-harga.index'),
                'is_dropdown' => false,

            ],
            [
                'label' => 'Transaksi Keluar',
                'icon' => 'fas fa-dollar-sign',
                'route' => '#',
                'is_active' => request()->routeIs('transaksi-keluar.*'),
                'is_dropdown' => true,
                'items' => [
                    [
                        'label' => 'Transaksi Baru',
                        'route' => 'transaksi-keluar.create'
                    ],
                    [
                        'label' => 'Data Transaksi',
                        'route' => 'transaksi-keluar.index'
                    ]
                ]

            ]
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
