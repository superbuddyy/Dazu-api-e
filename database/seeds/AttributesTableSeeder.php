<?php

declare(strict_types=1);

use App\Enums\OfferStatus;
use App\Enums\AttributeType;
use App\Enums\AttributeUnit;
use App\Models\Offer;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttributesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $type = Attribute::create([
            'name' => 'Typ',
            'description' => 'Typ ogłoszenia',
            'type' => AttributeType::STRING,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => ' na sprzedaż',
            'slug' => 'sprzedaz',
            'attribute_id' => $type->id
        ]);

        AttributeOption::create([
            'name' => 'na wynajem',
            'slug' => 'wynajem',
            'attribute_id' => $type->id
        ]);

        AttributeOption::create([
            'name' => 'do zamiany',
            'slug' => 'zamiana',
            'attribute_id' => $type->id
        ]);

        AttributeOption::create([
            'name' => 'do oddania',
            'slug' => 'oddanie',
            'attribute_id' => $type->id
        ]);

        Attribute::create([
            'name' => 'Na raty',
            'description' => 'Na raty',
            'type' => AttributeType::BOOLEAN,
            'unit' => AttributeUnit::NONE,
        ]);

        Attribute::create([
            'name' => 'Cena za m2',
            'description' => 'Cena za metr kwadratowy',
            'type' => AttributeType::INTEGER,
            'unit' => AttributeUnit::PLN,
        ]);

        Attribute::create([
            'name' => 'Metraż',
            'description' => 'Ilość m2',
            'type' => AttributeType::INTEGER,
            'unit' => AttributeUnit::SQUARE_M,
        ]);

        Attribute::create([
            'name' => 'zł/mc',
            'description' => 'Cena za miesiąc',
            'type' => AttributeType::BOOLEAN,
            'unit' => AttributeUnit::NONE,
        ]);

        Attribute::create([
            'name' => 'Do nagocjacji',
            'description' => 'Do nagocjacji',
            'type' => AttributeType::BOOLEAN,
            'unit' => AttributeUnit::NONE,
        ]);

        Attribute::create([
            'name' => 'Rachunki wliczone',
            'description' => 'Rachunki wliczone',
            'type' => AttributeType::BOOLEAN,
            'unit' => AttributeUnit::NONE,
        ]);

        Attribute::create([
            'name' => 'Darmowe',
            'description' => 'Darmowe',
            'type' => AttributeType::BOOLEAN,
            'unit' => AttributeUnit::NONE,
        ]);

        /** Pietro */
        $floor = Attribute::create([
            'name' => 'Piętro',
            'description' => 'Piętro na którym znajduje się nieruchomość',
            'type' => AttributeType::CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => 'parter',
            'slug' => 'parter',
            'attribute_id' => $floor->id
        ]);

        for ($i = 1; $i < 10; $i++) {
            AttributeOption::create([
                'name' => $i,
                'slug' => $i,
                'attribute_id' => $floor->id
            ]);
        }

        AttributeOption::create([
            'name' => 'powyżej 10',
            'slug' => 'powyzej_10',
            'attribute_id' => $floor->id
        ]);

        /** Ilość Pięter */

        $amountOfFloors = Attribute::create([
            'name' => 'Ilość pięter',
            'description' => 'Ilość pięter w budynku',
            'type' => AttributeType::CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        for ($i = 1; $i < 10; $i++) {
            AttributeOption::create([
                'name' => $i,
                'slug' => $i,
                'attribute_id' => $amountOfFloors->id
            ]);
        }

        AttributeOption::create([
            'name' => 'więcej niż 10',
            'slug' => 'wiecej_niz_10',
            'attribute_id' => $amountOfFloors->id
        ]);

        /** Ilość pomieszczeń */

        $amountOfRooms = Attribute::create([
            'name' => 'Ilość pokojów/pomieszczeń',
            'description' => 'Ilość pięter w budynku',
            'type' => AttributeType::CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        for ($i = 1; $i < 10; $i++) {
            AttributeOption::create([
                'name' => $i,
                'slug' => $i,
                'attribute_id' => $amountOfRooms->id
            ]);
        }

        AttributeOption::create([
            'name' => 'więcej niż 10',
            'slug' => 'wiecej_niz_10',
            'attribute_id' => $amountOfRooms->id
        ]);

        /** Stan */

        $state = Attribute::create([
            'name' => 'Stan',
            'description' => 'Stan nieruchomości',
            'type' => AttributeType::CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => 'Surowy',
            'slug' => 'surowy',
            'attribute_id' => $state->id
        ]);

        AttributeOption::create([
            'name' => 'Do remontu',
            'slug' => 'do_remontu',
            'attribute_id' => $state->id
        ]);

        AttributeOption::create([
            'name' => 'Do odświeżenia',
            'slug' => 'do_odswiezenia',
            'attribute_id' => $state->id
        ]);

        AttributeOption::create([
            'name' => 'Wyremontowane',
            'slug' => 'wyremontowane',
            'attribute_id' => $state->id
        ]);

        /** ------- */

        Attribute::create([
            'name' => 'Dostępne od zaraz',
            'description' => 'Nieuchomość dostępna od zaraz',
            'type' => AttributeType::BOOLEAN,
            'unit' => AttributeUnit::NONE,
        ]);

        Attribute::create([
            'name' => 'Dostępne od',
            'description' => 'Nieuchomość dostępna od',
            'type' => AttributeType::DATE,
            'unit' => AttributeUnit::NONE,
        ]);

        /** Na okres */
        $timeRange = Attribute::create([
            'name' => 'Na okres',
            'description' => 'Na okres',
            'type' => AttributeType::MULTI_CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => 'Godzinny',
            'slug' => 'godzinny',
            'attribute_id' => $timeRange->id
        ]);

        AttributeOption::create([
            'name' => 'Dniowy',
            'slug' => 'dniowy',
            'attribute_id' => $timeRange->id
        ]);

        AttributeOption::create([
            'name' => '1-3 mcy',
            'slug' => '1-3mcy',
            'attribute_id' => $timeRange->id
        ]);

        AttributeOption::create([
            'name' => '3-6 mcy',
            'slug' => '3-6mcy',
            'attribute_id' => $timeRange->id
        ]);

        AttributeOption::create([
            'name' => '6-12 mcy',
            'slug' => '6-12mcy',
            'attribute_id' => $timeRange->id
        ]);

        AttributeOption::create([
            'name' => 'Powyżej',
            'slug' => 'powyzej',
            'attribute_id' => $timeRange->id
        ]);

        /** Dodatkowe */

        $additional = Attribute::create([
            'name' => 'Dodatkowe',
            'description' => 'Dodatkowe',
            'type' => AttributeType::MULTI_CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => 'Piwnica',
            'slug' => 'piwnica',
            'attribute_id' => $additional->id
        ]);

        AttributeOption::create([
            'name' => 'Garaż',
            'slug' => 'garaz',
            'attribute_id' => $additional->id
        ]);

        AttributeOption::create([
            'name' => 'Parking',
            'slug' => 'parking',
            'attribute_id' => $additional->id
        ]);

        AttributeOption::create([
            'name' => 'Umeblowane',
            'slug' => 'umeblowane',
            'attribute_id' => $additional->id
        ]);

        AttributeOption::create([
            'name' => 'Winda',
            'slug' => 'winda',
            'attribute_id' => $additional->id
        ]);

        AttributeOption::create([
            'name' => 'Dostosowane do niepełnosprawnych',
            'slug' => 'dostosowane_do_niepelnosprawnych',
            'attribute_id' => $additional->id
        ]);

        AttributeOption::create([
            'name' => 'Kominek',
            'slug' => 'kominek',
            'attribute_id' => $additional->id
        ]);

        /** Blisko */

        $near = Attribute::create([
            'name' => 'Blisko',
            'description' => 'Blisko nieruchomości znajduje się',
            'type' => AttributeType::MULTI_CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => 'Siłownia',
            'slug' => 'siłownia',
            'attribute_id' => $near->id
        ]);

        AttributeOption::create([
            'name' => 'Szpital',
            'slug' => 'szpital',
            'attribute_id' => $near->id
        ]);

        AttributeOption::create([
            'name' => 'Centrum',
            'slug' => 'centrum',
            'attribute_id' => $near->id
        ]);

        AttributeOption::create([
            'name' => 'Sklep',
            'slug' => 'sklep',
            'attribute_id' => $near->id
        ]);

        AttributeOption::create([
            'name' => 'Szkoła',
            'slug' => 'szkola',
            'attribute_id' => $near->id
        ]);

        AttributeOption::create([
            'name' => 'Przystanek autobusowy',
            'slug' => 'przystanek_autobusowy',
            'attribute_id' => $near->id
        ]);

        AttributeOption::create([
            'name' => 'Przystanek tramwajnowy',
            'slug' => 'przystanek_tramwajowy',
            'attribute_id' => $near->id
        ]);

        AttributeOption::create([
            'name' => 'Dworzec',
            'slug' => 'dworzec',
            'attribute_id' => $near->id
        ]);

        /** Rok budowy */
        $year = Attribute::create([
            'name' => 'Rok budowy',
            'description' => 'Blisko nieruchomości znajduje się',
            'type' => AttributeType::CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => 'Jeszcze nie wybudowane',
            'slug' => 'jeszcze_nie_wybudowane',
            'attribute_id' => $year->id
        ]);

        AttributeOption::create([
            'name' => '2020 i powyżej',
            'slug' => '2020_i_powyżej',
            'attribute_id' => $year->id
        ]);

        AttributeOption::create([
            'name' => '2000-2019',
            'slug' => '2000-2019',
            'attribute_id' => $year->id
        ]);

        AttributeOption::create([
            'name' => '1990-2000',
            'slug' => '1990-2000',
            'attribute_id' => $year->id
        ]);

        AttributeOption::create([
            'name' => '1975-1990',
            'slug' => '1975-1990',
            'attribute_id' => $year->id
        ]);

        AttributeOption::create([
            'name' => '1950-1975',
            'slug' => '1950-1975',
            'attribute_id' => $year->id
        ]);

        AttributeOption::create([
            'name' => '1900-1950',
            'slug' => '1900-1950',
            'attribute_id' => $year->id
        ]);

        AttributeOption::create([
            'name' => 'Przed 1900',
            'slug' => 'przed_1900',
            'attribute_id' => $year->id
        ]);

        /** Rynek */

        $market = Attribute::create([
            'name' => 'Rynek',
            'description' => 'Rynek',
            'type' => AttributeType::CHOICE,
            'unit' => AttributeUnit::NONE,
        ]);

        AttributeOption::create([
            'name' => 'Wtórny',
            'slug' => 'wtorny',
            'attribute_id' => $market->id
        ]);

        AttributeOption::create([
            'name' => 'Pierwotny',
            'slug' => 'pierwotny',
            'attribute_id' => $market->id
        ]);

        Attribute::create([
            'name' => 'Pilne',
            'description' => 'Ogłoszenie jest pilne',
            'type' => AttributeType::BOOLEAN,
            'unit' => AttributeUnit::NONE,
        ]);
    }
}
