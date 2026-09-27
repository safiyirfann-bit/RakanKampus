<?php

/*
|--------------------------------------------------------------------------
| PUO matric numbers -> programme
|--------------------------------------------------------------------------
| A PUO matric number looks like 01DIT24F1128:
|   01    = institution code (01 = Politeknik Ungku Omar)
|   DIT   = programme code
|   24    = intake year
|   F     = intake session
|   1128  = running number
|
| The programme code is looked up below to show the student's programme
| under their name (official PUO programme codes). To add or rename a
| programme, just edit this list — nothing else needs changing.
*/

return [
    // Only matric numbers from this institution can register.
    'institution_code' => '01',

    'codes' => [
        'BBE' => 'Sarjana Muda Teknologi Kejuruteraan Awam',
        'BEE' => 'Sarjana Muda Teknologi Kejuruteraan Elektrik',
        'BME' => 'Sarjana Muda Teknologi Kejuruteraan Mekanikal',
        'DAD' => 'Diploma Kejuruteraan Mekanikal (Automotif)',
        'DAT' => 'Diploma Perakaunan',
        'DKA' => 'Diploma Kejuruteraan Awam',
        'DKM' => 'Diploma Kejuruteraan Mekanikal',
        'DMR' => 'Diploma Kejuruteraan Perkapalan (Marin)',
        'DPM' => 'Diploma Pengajian Perniagaan',
        'DPR' => 'Diploma Pemasaran',
        'DPT' => 'Diploma Kejuruteraan Mekanikal (Loji Pembungkusan)',
        'DRE' => 'Diploma Kejuruteraan Mekanikal (Penyejukan & Penyamanan Udara)',
        'DTK' => 'Diploma Kejuruteraan Komputer',
        'DUB' => 'Diploma Senibina',
        'DEE' => 'Diploma Kejuruteraan Elektrik (Kawalan)',
        'DET' => 'Diploma Kejuruteraan Elektrik & Elektronik',
        'DFB' => 'Diploma Kewangan & Perbankan Islam',
        'DGE' => 'Diploma Geomatik',
        'DIT' => 'Diploma Teknologi Maklumat',
        'DTP' => 'Diploma Kejuruteraan Mekanikal (Pembuatan)',
        'DEM' => 'Diploma Kejuruteraan Mekatronik',
    ],
];
