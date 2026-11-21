<?php

namespace Database\Seeders;

use App\Models\Bahan;
use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bahanMap = Bahan::pluck('id_bahan', 'nama_bahan')->toArray();

        $daftar_produk = [
            // ==================== SALAD ====================
            [
                'nama_produk' => 'Caesar Chicken Salad',
                'deskripsi' => 'Salad segar dengan irisan grilled chicken empuk, selada romaine renyah, keju parmesan, dan dressing caesar gurih.',
                'kategori' => 'Salad',
                'harga_offline' => 26000,
                'harga_online' => 29000,
                'foto' => 'produk/caesar_chicken_salad.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Romaine', 'Grilled Chicken', 'Telur'],
            ],
            [
                'nama_produk' => 'Japanese Sesame Salad',
                'deskripsi' => 'Kombinasi selada segar, kol ungu, tomat, dan timun dengan siraman dressing wijen sangrai khas Jepang yang gurih creamy.',
                'kategori' => 'Salad',
                'harga_offline' => 24000,
                'harga_online' => 27000,
                'foto' => 'produk/japanese_sesame_salad.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Selada', 'Kol Ungu', 'Tomat', 'Timun'],
            ],
            [
                'nama_produk' => 'Garden Salad Bowl',
                'deskripsi' => 'Salad sayuran warna-warni kaya serat dengan selada, tomat, timun, kacang polong, dan mixed vegetables bernutrisi.',
                'kategori' => 'Salad',
                'harga_offline' => 20000,
                'harga_online' => 23000,
                'foto' => 'produk/garden_veggie_salad.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => true,
                'id_cabang' => null,
                'bahan' => ['Selada', 'Kol Ungu', 'Tomat', 'Timun', 'Kacang Polong', 'Mixed Vegetables'],
            ],
            [
                'nama_produk' => 'Beef Patties Warm Salad',
                'deskripsi' => 'Menu eksklusif cabang MERR. Double beef patties gurih berpadu dengan selada romaine, kol ungu, dan telur rebus dengan honey mustard.',
                'kategori' => 'Salad',
                'harga_offline' => 32000,
                'harga_online' => 36000,
                'foto' => 'produk/beef_warm_salad.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => 2, // Cabang MERR
                'bahan' => ['Selada', 'Romaine', 'Kol Ungu', 'Beef Patties', 'Telur'],
            ],
            [
                'nama_produk' => 'Fresh Salad Roll',
                'deskripsi' => 'Gulungan rice paper transparan berisi selada renyah, timun, kol ungu, dan chicken slice dengan cocolan saus wijen istimewa.',
                'kategori' => 'Salad',
                'harga_offline' => 22000,
                'harga_online' => 25000,
                'foto' => 'produk/fresh_salad_roll.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Selada', 'Kol Ungu', 'Timun', 'Chicken Patties'],
            ],

            // ==================== SANDWICH & WRAP ====================
            [
                'nama_produk' => 'Crispy Chicken Patty Sandwich',
                'deskripsi' => 'Roti gandum lembut dengan chicken patty renyah, selada romaine segar, irisan tomat, dan mayonnaise rendah kalori.',
                'kategori' => 'Sandwich',
                'harga_offline' => 23000,
                'harga_online' => 26000,
                'foto' => 'produk/crispy_chicken_sandwich.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Romaine', 'Tomat', 'Chicken Patties'],
            ],
            [
                'nama_produk' => 'Double Beef & Egg Sandwich',
                'deskripsi' => 'Sandwich tebal berisi double beef patties juicy, telur gurih, selada segar, dan saus spesial Hamta yang nikmat.',
                'kategori' => 'Sandwich',
                'harga_offline' => 28000,
                'harga_online' => 32000,
                'foto' => 'produk/double_beef_sandwich.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Selada', 'Tomat', 'Beef Patties', 'Telur'],
            ],
            [
                'nama_produk' => 'Egg & Mayo Healthy Toast',
                'deskripsi' => 'Roti panggang isi racikan telur rebus creamy mayo lembut dan selada segar untuk sarapan sehat bergizi tinggi.',
                'kategori' => 'Sandwich',
                'harga_offline' => 18000,
                'harga_online' => 21000,
                'foto' => 'produk/egg_mayo_sandwich.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Selada', 'Telur'],
            ],
            [
                'nama_produk' => 'Grilled Chicken Tortilla Wrap',
                'deskripsi' => 'Tortilla wrap hangat membungkus potongan grilled chicken empuk, selada renyah, kol ungu, dan saus yogurt garlic.',
                'kategori' => 'Sandwich',
                'harga_offline' => 25000,
                'harga_online' => 28000,
                'foto' => 'produk/grilled_chicken_wrap.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Selada', 'Kol Ungu', 'Grilled Chicken'],
            ],

            // ==================== JUICE & SMOOTHIES ====================
            [
                'nama_produk' => 'Green Detox Booster',
                'deskripsi' => 'Cold-pressed juice kombinasi apel hijau, timun segar, bayam, dan perasan lemon kaya antioksidan murni tanpa tambahan gula.',
                'kategori' => 'Juice',
                'harga_offline' => 16000,
                'harga_online' => 19000,
                'foto' => 'produk/green_detox_juice.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => ['Timun'],
            ],
            [
                'nama_produk' => 'Dragon Fruit Glow',
                'deskripsi' => 'Jus buah naga merah murni berpadu dengan nanas segar dan aroma daun mint yang menyegarkan dahaga dan kaya serat.',
                'kategori' => 'Juice',
                'harga_offline' => 16000,
                'harga_online' => 19000,
                'foto' => 'produk/dragon_fruit_glow_juice.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => [],
            ],
            [
                'nama_produk' => 'Sunshine Orange Juice',
                'deskripsi' => '100% perasan jeruk manis alami segar kaya vitamin C untuk menjaga daya tahan tubuh dan kebugaran sepanjang hari.',
                'kategori' => 'Juice',
                'harga_offline' => 15000,
                'harga_online' => 18000,
                'foto' => 'produk/sunshine_orange_juice.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => [],
            ],
            [
                'nama_produk' => 'Berry Good Smoothies',
                'deskripsi' => 'Smoothie kental kaya vitamin dari perpaduan stroberi, blueberry, pisang, dan madu murni yang manis asam segar bernutrisi tinggi.',
                'kategori' => 'Juice',
                'harga_offline' => 18000,
                'harga_online' => 22000,
                'foto' => 'produk/berry_good_smoothie.jpg',
                'status_aktif' => true,
                'bisa_dikustom' => false,
                'id_cabang' => null,
                'bahan' => [],
            ],
        ];

        foreach ($daftar_produk as $item) {
            $bahanList = $item['bahan'];
            unset($item['bahan']);

            $produk = Produk::updateOrCreate(
                ['nama_produk' => $item['nama_produk']],
                $item
            );

            // Hubungkan bahan produk
            $bahanIds = [];
            foreach ($bahanList as $namaBahan) {
                if (isset($bahanMap[$namaBahan])) {
                    $bahanIds[] = $bahanMap[$namaBahan];
                }
            }

            if (! empty($bahanIds)) {
                $produk->bahan()->sync($bahanIds);
            }
        }
    }
}
