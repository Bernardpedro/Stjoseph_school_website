<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SchoolContentSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('admission_requirement_items')->countAllResults() === 0) {
            $items = [
                'Completed application form.',
                "Copy of the child's birth certificate.",
                'Previous school report or transfer letter (if transferring from another school).',
                'Two passport-size photographs.',
                "Copy of the parent or guardian's national ID.",
                'Payment of the registration or application fee (if applicable).',
            ];
            foreach ($items as $i => $text) {
                $this->db->table('admission_requirement_items')->insert([
                    'item_text'  => $text,
                    'sort_order' => $i + 1,
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if ($this->db->table('requirement_levels')->countAllResults() === 0) {
            $levels = [
                [
                    'code'        => 'level-1',
                    'name'        => 'Level 1',
                    'description' => 'Short courses: Tailoring, Building Construction, Carpentry, Automobile Technology.',
                    'sort_order'  => 1,
                ],
                [
                    'code'        => 'level-3',
                    'name'        => 'Level 3 - Senior 4',
                    'description' => 'Technical programs: Building Construction and Automobile Technology.',
                    'sort_order'  => 2,
                ],
                [
                    'code'        => 'level-4',
                    'name'        => 'Level 4 - Senior 5',
                    'description' => 'Technical programs: Building Construction and Automobile Technology.',
                    'sort_order'  => 3,
                ],
                [
                    'code'        => 'level-5',
                    'name'        => 'Level 5 - Senior 6',
                    'description' => 'Technical programs: Building Construction and Automobile Technology.',
                    'sort_order'  => 4,
                ],
            ];
            foreach ($levels as $level) {
                $this->db->table('requirement_levels')->insert($level + [
                    'urls'       => json_encode([]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if ($this->db->table('events')->countAllResults() === 0) {
            $this->db->table('events')->insert([
                'title'       => 'Open Day at Saint Joseph TSS Nzuki',
                'description' => 'Visit our workshops and meet instructors for Automobile, Construction, Tailoring and Carpentry.',
                'date'        => date('Y-m-d', strtotime('+14 days')),
                'time'        => '09:00',
                'location'    => 'School campus, Nzuki',
                'type'        => 'Open Day',
                'status'      => 'upcoming',
                'organizer'   => 'Saint Joseph TSS Nzuki',
                'youtubeLink' => '',
                'images'      => json_encode([
                    'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
                ]),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $this->db->table('events')->insert([
                'title'       => 'Skills Exhibition',
                'description' => 'Students present practical work from technical programs.',
                'date'        => date('Y-m-d', strtotime('+30 days')),
                'time'        => '10:00',
                'location'    => 'Main hall',
                'type'        => 'Exhibition',
                'status'      => 'upcoming',
                'organizer'   => 'Saint Joseph TSS Nzuki',
                'youtubeLink' => '',
                'images'      => json_encode([
                    'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg',
                ]),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        if ($this->db->table('projects')->countAllResults() === 0) {
            $projects = [
                [
                    'title'       => 'Rheinland-Pfalz Partnership',
                    'description' => 'Long-term cooperation with Berufsbildende Schule Lahnstein and the Rheinland-Pfalz / Rwanda partnership. The project supports workshop equipment, teacher exchange, and student skills in technical trades.',
                    'partner'     => 'Partnerschaftsverein Rheinland-Pfalz/Ruanda e. V.',
                    'year'        => '2024–2026',
                    'status'      => 'Ongoing',
                    'category'    => 'Partnership',
                    'media'       => [
                        [
                            'type'      => 'image',
                            'url'       => 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752738861/rlp_ruanda_logo_jt9mcf.png',
                            'caption'   => 'Rheinland-Pfalz / Rwanda partnership',
                            'thumbnail' => '',
                        ],
                        [
                            'type'      => 'image',
                            'url'       => 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
                            'caption'   => 'Campus visit and collaboration',
                            'thumbnail' => '',
                        ],
                    ],
                ],
                [
                    'title'       => 'Automobile Workshop Upgrade',
                    'description' => 'Modernizing the automobile technology workshop so Level 3–5 students can practise engine diagnostics, servicing, and safety on current vehicles.',
                    'partner'     => 'Saint Joseph TSS Nzuki',
                    'year'        => '2025',
                    'status'      => 'Ongoing',
                    'category'    => 'Infrastructure',
                    'media'       => [
                        [
                            'type'      => 'image',
                            'url'       => 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752745560/moteur2Resized_pt3a83.jpg',
                            'caption'   => 'Automobile technology workshop',
                            'thumbnail' => '',
                        ],
                    ],
                ],
                [
                    'title'       => 'Building Construction Practice Site',
                    'description' => 'A hands-on construction yard where students learn masonry, measurement, and site safety while contributing to campus buildings.',
                    'partner'     => 'Diocese of Kabgayi',
                    'year'        => '2025–2026',
                    'status'      => 'Ongoing',
                    'category'    => 'Academics',
                    'media'       => [
                        [
                            'type'      => 'image',
                            'url'       => 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752774851/constr3_j4zg1g.jpg',
                            'caption'   => 'Students on the construction practice site',
                            'thumbnail' => '',
                        ],
                    ],
                ],
                [
                    'title'       => 'Carpentry and Tailoring Workshops',
                    'description' => 'Equipping Level 1 short-course workshops for carpentry and tailoring so learners can produce real items and prepare for local work.',
                    'partner'     => 'Saint Joseph TSS Nzuki',
                    'year'        => '2024',
                    'status'      => 'Completed',
                    'category'    => 'Skills',
                    'media'       => [
                        [
                            'type'      => 'image',
                            'url'       => 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752741665/carpentryWorkShop005_r3gocb.jpg',
                            'caption'   => 'Carpentry workshop',
                            'thumbnail' => '',
                        ],
                        [
                            'type'      => 'image',
                            'url'       => 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg',
                            'caption'   => 'Technical workshop training',
                            'thumbnail' => '',
                        ],
                    ],
                ],
            ];

            foreach ($projects as $project) {
                $this->db->table('projects')->insert([
                    'title'       => $project['title'],
                    'description' => $project['description'],
                    'partner'     => $project['partner'],
                    'year'        => $project['year'],
                    'status'      => $project['status'],
                    'category'    => $project['category'],
                    'media'       => json_encode($project['media']),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        if ($this->db->table('announcements')->countAllResults() === 0) {
            $this->db->table('announcements')->insert([
                'title'      => 'Admissions are open',
                'message'    => 'Apply now for Level 1 short courses and Levels 3–5 technical programs.',
                'cta_text'   => 'Apply Now',
                'link'       => '/admission',
                'sort_order' => 1,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $settings = [
            'requirements.title'         => 'School Requirements',
            'requirements.subtitle'      => 'Documents and materials needed for each level.',
            'requirements.academic_year' => date('Y') . '-' . (date('Y') + 1),
            'projects.title'             => 'Our Projects',
            'projects.description'       => 'Partnerships and campus development at Saint Joseph TSS Nzuki.',
        ];

        foreach ($settings as $key => $value) {
            $exists = $this->db->table('settings')->where('setting_key', $key)->get()->getRowArray();
            if (!$exists) {
                $this->db->table('settings')->insert([
                    'setting_key'   => $key,
                    'setting_value' => $value,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
    }
}
