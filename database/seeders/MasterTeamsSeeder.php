<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class MasterTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            // Premier League (20)
            ['name' => 'Manchester City',      'power_min' => 90, 'power_max' => 100, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Manchester_City_FC_badge.svg/500px-Manchester_City_FC_badge.svg.png', 'supporter_strength' => 85],
            ['name' => 'Liverpool',            'power_min' => 88, 'power_max' => 98,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Liverpool_FC.svg/500px-Liverpool_FC.svg.png', 'supporter_strength' => 95],
            ['name' => 'Arsenal',              'power_min' => 85, 'power_max' => 95,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/53/Arsenal_FC.svg/500px-Arsenal_FC.svg.png', 'supporter_strength' => 90],
            ['name' => 'Manchester United',    'power_min' => 80, 'power_max' => 90,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Manchester_United_FC_crest.svg/500px-Manchester_United_FC_crest.svg.png', 'supporter_strength' => 95],
            ['name' => 'Chelsea',              'power_min' => 78, 'power_max' => 88,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/cc/Chelsea_FC.svg/500px-Chelsea_FC.svg.png', 'supporter_strength' => 88],
            ['name' => 'Tottenham Hotspur',    'power_min' => 75, 'power_max' => 85,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b4/Tottenham_Hotspur.svg/500px-Tottenham_Hotspur.svg.png', 'supporter_strength' => 85],
            ['name' => 'Newcastle United',     'power_min' => 72, 'power_max' => 82,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Newcastle_United_Logo.svg/500px-Newcastle_United_Logo.svg.png', 'supporter_strength' => 92],
            ['name' => 'Aston Villa',          'power_min' => 70, 'power_max' => 80,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/5/57/Aston_Villa.png', 'supporter_strength' => 82],
            ['name' => 'West Ham United',      'power_min' => 68, 'power_max' => 78,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c2/West_Ham_United_FC_logo.svg/500px-West_Ham_United_FC_logo.svg.png', 'supporter_strength' => 85],
            ['name' => 'Brighton & Hove Albion','power_min'=>65, 'power_max' => 75, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/thumb/e/e3/Brighton%26Hove.png/330px-Brighton%26Hove.png', 'supporter_strength' => 75],
            ['name' => 'Brentford',            'power_min' => 62, 'power_max' => 72,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/f/fe/Brentford_FC1.png', 'supporter_strength' => 70],
            ['name' => 'Fulham',               'power_min' => 60, 'power_max' => 70,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/thumb/4/40/Fulham.svg/330px-Fulham.svg.png', 'supporter_strength' => 68],
            ['name' => 'Crystal Palace',       'power_min' => 58, 'power_max' => 68,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/c/c5/Crystal_Palace_FC_logo_%282022%29.png', 'supporter_strength' => 75],
            ['name' => 'Nottingham Forest',    'power_min' => 55, 'power_max' => 65,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/8/85/Nottingham_Forest_FC.png', 'supporter_strength' => 80],
            ['name' => 'Bournemouth',          'power_min' => 52, 'power_max' => 62,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/9/98/AFC_Bournemouth.png', 'supporter_strength' => 65],
            ['name' => 'Leicester City',       'power_min' => 50, 'power_max' => 60,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/thumb/a/a2/Leicester_City_logo.png/330px-Leicester_City_logo.png', 'supporter_strength' => 75],
            ['name' => 'Wolverhampton Wanderers','power_min'=>48, 'power_max' => 58,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/thumb/6/6c/Wolverhampton_Wanderers.png/330px-Wolverhampton_Wanderers.png', 'supporter_strength' => 78],
            ['name' => 'Leeds United',         'power_min' => 45, 'power_max' => 55,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/7/78/Leeds_United_logo.png', 'supporter_strength' => 85],
            ['name' => 'Southampton',          'power_min' => 42, 'power_max' => 52,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/thumb/1/16/Southampton_logo_2010.png/330px-Southampton_logo_2010.png', 'supporter_strength' => 70],
            ['name' => 'Everton',              'power_min' => 40, 'power_max' => 50,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/tr/7/79/Everton_Logo.png', 'supporter_strength' => 82],

            // La Liga (20)
            ['name' => 'Real Madrid',          'power_min' => 92, 'power_max' => 100, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/500px-Real_Madrid_CF.svg.png', 'supporter_strength' => 95],
            ['name' => 'Barcelona',            'power_min' => 90, 'power_max' => 98,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/500px-FC_Barcelona_%28crest%29.svg.png', 'supporter_strength' => 95],
            ['name' => 'Atletico Madrid',      'power_min' => 85, 'power_max' => 95,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f9/Atletico_Madrid_Logo_2024.svg/558px-Atletico_Madrid_Logo_2024.svg.png?20240708000437', 'supporter_strength' => 90],
            ['name' => 'Sevilla',              'power_min' => 82, 'power_max' => 92,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/3/3b/Sevilla_FC_logo.svg/330px-Sevilla_FC_logo.svg.png', 'supporter_strength' => 88],
            ['name' => 'Real Sociedad',        'power_min' => 78, 'power_max' => 88,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f1/Real_Sociedad_logo.svg/360px-Real_Sociedad_logo.svg.png', 'supporter_strength' => 82],
            ['name' => 'Villarreal',           'power_min' => 75, 'power_max' => 85,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b9/Villarreal_CF_logo-en.svg/350px-Villarreal_CF_logo-en.svg.png', 'supporter_strength' => 75],
            ['name' => 'Athletic Bilbao',      'power_min' => 72, 'power_max' => 82,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/9/98/Club_Athletic_Bilbao_logo.svg/350px-Club_Athletic_Bilbao_logo.svg.png', 'supporter_strength' => 85],
            ['name' => 'Valencia',             'power_min' => 70, 'power_max' => 80,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Valenciacf.svg/380px-Valenciacf.svg.png', 'supporter_strength' => 88],
            ['name' => 'Rayo Vallecano',       'power_min' => 58, 'power_max' => 68,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d8/Rayo_Vallecano_logo.svg/400px-Rayo_Vallecano_logo.svg.png', 'supporter_strength' => 80],
            ['name' => 'Girona',               'power_min' => 55, 'power_max' => 65,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f7/Girona_FC_Logo.svg/400px-Girona_FC_Logo.svg.png', 'supporter_strength' => 70],
            ['name' => 'Getafe',               'power_min' => 52, 'power_max' => 62,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/4/46/Getafe_logo.svg/400px-Getafe_logo.svg.png', 'supporter_strength' => 65],
            ['name' => 'Espanyol',             'power_min' => 50, 'power_max' => 60,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/9/92/RCD_Espanyol_crest.svg/310px-RCD_Espanyol_crest.svg.png', 'supporter_strength' => 75],
            ['name' => 'Alaves',               'power_min' => 48, 'power_max' => 58,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f8/Deportivo_Alaves_logo_%282020%29.svg/400px-Deportivo_Alaves_logo_%282020%29.svg.png', 'supporter_strength' => 70],
            ['name' => 'Cadiz',                'power_min' => 45, 'power_max' => 55,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/58/C%C3%A1diz_CF_logo.svg/300px-C%C3%A1diz_CF_logo.svg.png', 'supporter_strength' => 75],
            ['name' => 'Real Valladolid',      'power_min' => 42, 'power_max' => 52,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a9/Real_Valladolid_CF_crest.svg/400px-Real_Valladolid_CF_crest.svg.png', 'supporter_strength' => 72],
            ['name' => 'Elche',                'power_min' => 40, 'power_max' => 50,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a7/Elche_CF_logo.svg/360px-Elche_CF_logo.svg.png', 'supporter_strength' => 68],

            // Bundesliga (12)
            ['name' => 'Bayern Munich',        'power_min' => 90, 'power_max' => 100, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/FC_Bayern_M%C3%BCnchen_logo_%282024%29.svg/500px-FC_Bayern_M%C3%BCnchen_logo_%282024%29.svg.png', 'supporter_strength' => 95],
            ['name' => 'Borussia Dortmund',    'power_min' => 85, 'power_max' => 95,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/500px-Borussia_Dortmund_logo.svg.png', 'supporter_strength' => 95],
            ['name' => 'RB Leipzig',           'power_min' => 82, 'power_max' => 92,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/04/RB_Leipzig_2014_logo.svg/620px-RB_Leipzig_2014_logo.svg.png', 'supporter_strength' => 75],
            ['name' => 'Bayer Leverkusen',     'power_min' => 78, 'power_max' => 88,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/59/Bayer_04_Leverkusen_logo.svg/480px-Bayer_04_Leverkusen_logo.svg.png', 'supporter_strength' => 80],
            ['name' => 'Eintracht Frankfurt',  'power_min' => 75, 'power_max' => 85,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7e/Eintracht_Frankfurt_crest.svg/420px-Eintracht_Frankfurt_crest.svg.png', 'supporter_strength' => 85],
            ['name' => 'VfL Wolfsburg',        'power_min' => 72, 'power_max' => 82,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/VfL_Wolfsburg_Logo.svg/380px-VfL_Wolfsburg_Logo.svg.png', 'supporter_strength' => 70],
            ['name' => 'Borussia Mgladbach',   'power_min' => 70, 'power_max' => 80,  'logo_url' => 'https://en.wikipedia.org/wiki/Borussia_M%C3%B6nchengladbach#/media/File:Borussia_M%C3%B6nchengladbach_logo.svg', 'supporter_strength' => 82],
            ['name' => 'VfB Stuttgart',        'power_min' => 68, 'power_max' => 78,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/VfB_Stuttgart_1893_Logo.svg/380px-VfB_Stuttgart_1893_Logo.svg.png', 'supporter_strength' => 80],
            ['name' => 'Union Berlin',         'power_min' => 65, 'power_max' => 75,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/1._FC_Union_Berlin_Logo.svg/500px-1._FC_Union_Berlin_Logo.svg.png', 'supporter_strength' => 85],
            ['name' => 'SC Freiburg',          'power_min' => 62, 'power_max' => 72,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/6/6d/SC_Freiburg_logo.svg/310px-SC_Freiburg_logo.svg.png', 'supporter_strength' => 75],
            ['name' => '1. FC Köln',           'power_min' => 60, 'power_max' => 70,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/1._FC_Koeln_Logo_2014%E2%80%93.svg/400px-1._FC_Koeln_Logo_2014%E2%80%93.svg.png', 'supporter_strength' => 82],
            ['name' => 'FC Augsburg',          'power_min' => 58, 'power_max' => 68,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c5/FC_Augsburg_logo.svg/290px-FC_Augsburg_logo.svg.png', 'supporter_strength' => 70],

            // Serie A (12)
            ['name' => 'Inter Milan',          'power_min' => 88, 'power_max' => 98,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/FC_Internazionale_Milano_2021.svg/330px-FC_Internazionale_Milano_2021.svg.png', 'supporter_strength' => 90],
            ['name' => 'Juventus',             'power_min' => 82, 'power_max' => 92,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ed/Juventus_FC_-_logo_black_%28Italy%2C_2020%29.svg/250px-Juventus_FC_-_logo_black_%28Italy%2C_2020%29.svg.png', 'supporter_strength' => 92],
            ['name' => 'Napoli',               'power_min' => 80, 'power_max' => 90,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/SSC_Napoli_2024_%28deep_blue_navy%29.svg/500px-SSC_Napoli_2024_%28deep_blue_navy%29.svg.png', 'supporter_strength' => 88],
            ['name' => 'AS Roma',              'power_min' => 78, 'power_max' => 88,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f7/AS_Roma_logo_%282017%29.svg/500px-AS_Roma_logo_%282017%29.svg.png', 'supporter_strength' => 85],
            ['name' => 'Lazio',                'power_min' => 75, 'power_max' => 85,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/S.S._Lazio_badge.svg/500px-S.S._Lazio_badge.svg.png', 'supporter_strength' => 82],
            ['name' => 'Torino',               'power_min' => 65, 'power_max' => 75,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2e/Torino_FC_Logo.svg/330px-Torino_FC_Logo.svg.png', 'supporter_strength' => 75],
            ['name' => 'Sassuolo',             'power_min' => 62, 'power_max' => 72,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/1c/US_Sassuolo_Calcio_logo.svg/380px-US_Sassuolo_Calcio_logo.svg.png', 'supporter_strength' => 65],
            ['name' => 'Udinese',              'power_min' => 60, 'power_max' => 70,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Udinese_Calcio_logo.svg/420px-Udinese_Calcio_logo.svg.png', 'supporter_strength' => 70],

            // Süper Lig (19)
            ['name' => 'Galatasaray',          'power_min' => 75, 'power_max' => 85,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Galatasaray_4_Sterne_Logo.svg/340px-Galatasaray_4_Sterne_Logo.svg.png', 'supporter_strength' => 95],
            ['name' => 'Fenerbahçe',           'power_min' => 74, 'power_max' => 84,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/3/39/Fenerbah%C3%A7e.svg/420px-Fenerbah%C3%A7e.svg.png', 'supporter_strength' => 95],
            ['name' => 'Beşiktaş',             'power_min' => 72, 'power_max' => 82,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/BesiktasJK-Logo.svg/420px-BesiktasJK-Logo.svg.png', 'supporter_strength' => 95],
            ['name' => 'Trabzonspor',          'power_min' => 70, 'power_max' => 80,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/de/Trabzonspor_Amblem.svg/300px-Trabzonspor_Amblem.svg.png', 'supporter_strength' => 90],
            ['name' => 'Başakşehir',           'power_min' => 68, 'power_max' => 78,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e1/%C4%B0stanbul_Ba%C5%9Fak%C5%9Fehir_logo.svg/310px-%C4%B0stanbul_Ba%C5%9Fak%C5%9Fehir_logo.svg.png', 'supporter_strength' => 65],
            ['name' => 'Alanyaspor',           'power_min' => 65, 'power_max' => 75,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/4/40/Alanyaspor_logo.svg/360px-Alanyaspor_logo.svg.png', 'supporter_strength' => 60],
            ['name' => 'Gaziantep FK',            'power_min' => 52, 'power_max' => 62,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c6/Gazi%C5%9Fehir_Gaziantep_logo.svg/330px-Gazi%C5%9Fehir_Gaziantep_logo.svg.png', 'supporter_strength' => 70],
            ['name' => 'Antalyaspor',              'power_min' => 50, 'power_max' => 60,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/83/Antalyaspor_logo.svg/350px-Antalyaspor_logo.svg.png', 'supporter_strength' => 65],
            ['name' => 'Hatayspor',            'power_min' => 48, 'power_max' => 58,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fe/Hatayspor_crest.png', 'supporter_strength' => 60],
            ['name' => 'Eyüpspor',             'power_min' => 35, 'power_max' => 45,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/4/48/Eyupspor_logo.png', 'supporter_strength' => 55],
            ['name' => 'Bodrumspor',               'power_min' => 32, 'power_max' => 42,  'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c1/Bodrum_FK_crest.svg/360px-Bodrum_FK_crest.svg.png', 'supporter_strength' => 50],
        ];

        foreach ($teams as $data) {
            Team::updateOrCreate(
                ['name' => $data['name']],
                [
                    'power_min' => $data['power_min'],
                    'power_max' => $data['power_max'],
                    'logo' => $data['logo_url'],
                    'supporter_strength' => $data['supporter_strength']
                ]
            );
        }
    }
}
