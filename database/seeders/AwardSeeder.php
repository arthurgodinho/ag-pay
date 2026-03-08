<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Award;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $awards = [
            [
                'title' => 'Placa de 50K',
                'description' => 'Prêmio Exclusivo ao atingir R$ 50.000,00 em vendas acumuladas!',
                'goal_amount' => 50000.00,
                'image_url' => 'https://i.imgur.com/3T40GNL.png',
            ],
            [
                'title' => 'Placa de 100K',
                'description' => 'Prêmio Exclusivo ao atingir R$ 100.000,00 em vendas acumuladas!',
                'goal_amount' => 100000.00,
                'image_url' => 'https://i.imgur.com/3T40GNL.png',
            ],
            [
                'title' => 'Placa de 500K',
                'description' => 'Prêmio Exclusivo ao atingir R$ 500.000,00 em vendas acumuladas!',
                'goal_amount' => 500000.00,
                'image_url' => 'https://i.imgur.com/3T40GNL.png',
            ],
            [
                'title' => 'Placa de 1M',
                'description' => 'Prêmio Exclusivo ao atingir R$ 1.000.000,00 em vendas acumuladas!',
                'goal_amount' => 1000000.00,
                'image_url' => 'https://i.imgur.com/3T40GNL.png',
            ],
            [
                'title' => 'Placa de 5M',
                'description' => 'Prêmio Exclusivo ao atingir R$ 5.000.000,00 em vendas acumuladas!',
                'goal_amount' => 5000000.00,
                'image_url' => 'https://i.imgur.com/3T40GNL.png',
            ],
        ];

        foreach ($awards as $awardData) {
            // Baixar a imagem e salvar localmente
            $imageContents = file_get_contents($awardData['image_url']);
            $imageName = Str::random(40) . '.png';
            $path = 'awards/' . $imageName;
            Storage::disk('public')->put($path, $imageContents);

            // Usar updateOrCreate para evitar duplicatas
            Award::updateOrCreate(
                ['title' => $awardData['title']],
                [
                    'description' => $awardData['description'],
                    'goal_amount' => $awardData['goal_amount'],
                    'image_url' => $path,
                ]
            );
        }
    }
}
