<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        $names = ['Kamar A1','Kamar A2','Kamar B1','Kamar B2','Kamar C1','Kamar C2'];
        $kode  = strtoupper($this->faker->bothify('??-##'));
        $fac   = $this->faker->randomElements(
            ['AC','KM Dalam','Wifi','Water Heater','Kasur','Lemari','Meja','Parkir'], 
            $this->faker->numberBetween(3,6)
        );

        return [
            'kode'       => $kode,
            'nama'       => $this->faker->randomElement($names),
            'harga'      => $this->faker->numberBetween(600000, 1500000),
            'tersedia'   => $this->faker->boolean(75),
            'fasilitas'  => $fac,
            'deskripsi'  => $this->faker->sentence(12),
            'foto_path'  => null, // nanti bisa upload sendiri
        ];
    }
}
