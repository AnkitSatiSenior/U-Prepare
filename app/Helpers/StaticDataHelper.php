<?php

namespace App\Helpers;

class StaticDataHelper
{
    /**
     * Static Citizen Corner Data
     */
    public static function citizenCornerData(): array
    {
        return [(object) ['img' => 'project-status.webp', 'name' => 'Projects Status', 'link' => '/login'], (object) ['img' => 'tenders.webp', 'name' => 'Tenders & Notice', 'link' => '/en/tenders'], (object) ['img' => 'grievance-register.webp', 'name' => 'Grievance Register', 'link' => '/en/grievance'], (object) ['img' => 'grievance-status.webp', 'name' => 'Grievance Status', 'link' => '/en/grievance/status'], (object) ['img' => 'vacancies.webp', 'name' => 'Vacancies', 'link' => '/en/vacancies'], (object) ['img' => 'suggestions.webp', 'name' => 'Suggestions', 'link' => '/en/suggestions']];
    }

    /**
     * Static Past Projects Data
     */
    public static function pastProjectsData(): array
    {
        return [
            (object) [
                'img' => 'assets/img/pps-bgi.webp',
                'bgc' => 'udrp',
                'title' => 'UDRP: Uttarakhand Disaster Recovery Project (2014-2019)',
                'name' => 'Past Projects',
                'link' => '#',
                'link_txt' => 'Learn More',
            ],
            (object) [
                'img' => 'assets/img/pps-bgi-af.webp',
                'bgc' => 'udrpaf',
                'title' => 'UDRP-AF: Uttarakhand Disaster Recovery Project - AF (2019-2023)',
                'name' => 'Past Projects',
                'link' => '#',
                'link_txt' => 'Learn More',
            ],
        ];
    }
    public static function typology($slug = null, $item = false)
    {
        $typologies = [
            (object) [
                'name' => 'Bridges & Approach Road (PWD)',
                'slug' => 'bridges-and-approach-road-pwd',
                'dept' => 'PIU-PWD',
            ],
            (object) [
                'name' => 'Slope Protection (PWD)',
                'slug' => 'slope-protection-pwd',
                'dept' => 'PIU-PWD',
            ],
            (object) [
                'name' => 'Construction of Building/Fire Stations/Fire Training Centre (RWD)',
                'slug' => 'construction-of-building-fire-stations-fire-training-centre-rwd',
                'dept' => 'PIU-RWD',
            ],
            (object) [
                'name' => 'Forest Fire Management (Forest/USDMA)',
                'slug' => 'forest-fire-management-forest-usdma',
                'dept' => 'PIU-FOREST',
            ],
            (object) [
                'name' => 'Other',
                'slug' => 'other',
                'dept' => null,
            ],
        ];

        // 🔹 Return all typologies if no slug is given
        if (!$slug) {
            return $typologies;
        }

        // 🔹 Find by slug
        foreach ($typologies as $typology) {
            if ($typology->slug === $slug) {
                return $item ? $typology : $typology->name;
            }
        }

        // 🔹 Not found
        return null;
    }
}
