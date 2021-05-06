<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(11);
        Category::create([
            'name' => 'Mieszkanie',
            'children' => [
                ['name' => 'Blok'],
                ['name' => 'Kamienica'],
                ['name' => 'Loft'],
                ['name' => 'Apartamentowiec'],
                ['name' => 'Studio'],
                ['name' => 'Wieżowiec'],
                ['name' => 'Inne'],
            ],
        ]);

        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Dom',
            'children' => [
                ['name' => 'Kamienica'],
                ['name' => 'Dom letniskowy'],
                ['name' => 'Jednorodzinny'],
                ['name' => 'Bliźniak'],
                ['name' => 'Szeregowiec'],
                ['name' => 'Wolnostojący'],
                ['name' => 'Inne'],
            ],
        ]);
        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Dom modularowany',
            'children' => [
                ['name' => 'Metal'],
                ['name' => 'Drewno'],
                ['name' => 'Inny materiał'],
            ],
        ]);
        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Lokal użytkowy',
            'children' => [
                ['name' => 'Biuro'],
                ['name' => 'Budynek handlowo-usługowy'],
                ['name' => 'Budynek przemysłowy'],
                ['name' => 'Budynek gospodarczy lub produkcyjny'],
                ['name' => 'Budynek instytucjonalny, kulturalny lub sportowy'],
                ['name' => 'Sklep'],
                ['name' => 'Magazyn'],
                ['name' => 'Stoisko'],
                ['name' => 'Teren lub plac użytkowy'],
                ['name' => 'Inne'],
            ],
        ]);
        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Działka',
            'children' => [
                ['name' => 'Budowlana'],
                ['name' => 'Rolnicza'],
                ['name' => 'Rolniczo-budowlana'],
                ['name' => 'Siedliskowa'],
                ['name' => 'Rekreacyjna'],
                ['name' => 'Inna'],
            ],
        ]);
        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Kamping',
            'children' => [],
        ]);
        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Domek letniskowy',
            'children' => [],
        ]);
        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Garaż',
            'children' => [
                ['name' => 'Drewniany'],
                ['name' => 'Murowany'],
                ['name' => 'Metalowy'],
                ['name' => 'Wolnostojący'],
                ['name' => 'Podziemny'],
                ['name' => 'Inna'],
            ],
        ]);
        $this->command->getOutput()->progressAdvance();

        Category::create([
            'name' => 'Pokój',
            'children' => [
                ['name' => 'Bez łazienki'],
                ['name' => 'Z łazienką'],
                ['name' => 'Inna'],
            ],
        ]);
        $this->command->getOutput()->progressAdvance();

        $this->command->getOutput()->progressFinish();
    }
}
