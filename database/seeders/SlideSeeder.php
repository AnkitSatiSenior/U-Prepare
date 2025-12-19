<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slide;

class SlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'img'      => 'assets/img/slider/simg_01.webp',
                'head'     => 'Resilient Uttarakhand: Safeguarding Lives and Landscapes',
                'subh'     => 'Nestled amidst the majestic Himalayas, the Uttarakhand Disaster Management Authority stands vigilant, committed to protecting our communities and preserving our natural heritage',
                'link'     => null,
                'btn_text' => null,
            ],
            [
                'img'      => 'assets/img/slider/simg_02.webp',
                'head'     => 'Building Resilient Infrastructure',
                'subh'     => 'Fortifying foundations for a sustainable tomorrow. Strengthening structures and systems to withstand the test of time and nature.',
                'link'     => null,
                'btn_text' => null,
            ],
            [
                'img'      => 'assets/img/slider/simg_03.webp',
                'head'     => 'Elevating Emergency Preparedness and Response',
                'subh'     => 'Ready, resilient, responsive. Equipping communities with the tools and knowledge to face emergencies head-on, ensuring safety and swift action.',
                'link'     => null,
                'btn_text' => null,
            ],
            [
                'img'      => 'assets/img/slider/simg_04.webp',
                'head'     => 'Safeguarding Against Forest and General Fires',
                'subh'     => 'Guardians of green, protectors of progress. Combating fire hazards through vigilant prevention and effective management, safeguarding our natural and built environments.',
                'link'     => null,
                'btn_text' => null,
            ],
            [
                'img'      => 'assets/img/slider/simg_05.webp',
                'head'     => 'Excellence in Project Management',
                'subh'     => 'Strategizing success, executing with precision. Managing projects efficiently from inception to completion, ensuring impactful and sustainable outcomes.',
                'link'     => null,
                'btn_text' => null,
            ],
        ];

        foreach ($slides as $slide) {
            Slide::create($slide);
        }
    }
}
