<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 19.10.2016
 * Time: 11:41
 * http://www.filesignatures.net/index.php?page=all
 */
return [
    'jpe' =>[
        ['sig' => 'FF D8 FF E0', 'offset' => 0, 'desc' => 'JPEG IMAGE', 'enc' => 'ASCII'],
    ],
    'jpeg' =>[
        ['sig' => 'FF D8 FF E0', 'offset' => 0, 'desc' => 'JPEG IMAGE', 'enc' => 'ASCII'],
        ['sig' => 'FF D8 FF E2', 'offset' => 0, 'desc' => 'CANNON EOS JPEG FILE', 'enc' => 'ASCII'],
        ['sig' => 'FF D8 FF E3', 'offset' => 0, 'desc' => 'SAMSUNG D500 JPEG FILE', 'enc' => 'ASCII'],
//        ['sig' => 'FF D8 FF DB', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '4A 46', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '49 46 00 01', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '45 78', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '69 66 00 00', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
    ],
    'jpg' =>[
        ['sig' => 'FF D8 FF E0', 'offset' => 0, 'desc' => 'JPEG IMAGE', 'enc' => 'ASCII'],
        ['sig' => 'FF D8 FF E1', 'offset' => 0, 'desc' => 'Digital camera JPG using Exchangeable Image File Format (EXIF)', 'enc' => 'ASCII'],
        ['sig' => 'FF D8 FF E8', 'offset' => 0, 'desc' => 'Still Picture Interchange File Format (SPIFF)', 'enc' => 'ASCII'],
//        ['sig' => 'FF D8 FF DB', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '4A 46', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '49 46 00 01', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '45 78', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
//        ['sig' => '69 66 00 00', 'offset' => 0, 'desc' => 'from wiki', 'enc' => 'ASCII'],
    ],
    'png' =>[
        ['sig' => '89 50 4E 47 0D 0A 1A 0A', 'offset' => 0, 'desc' => 'PNG image', 'enc' => 'ASCII'],
    ],
    'gif' =>[
        ['sig' => '47 49 46 38 ', 'offset' => 0, 'desc' => 'GIF file', 'enc' => 'ASCII'],
    ],
    'tif' =>[
        ['sig' => '49 20 49', 'offset' => 0, 'desc' => 'TIFF file_1', 'enc' => 'ASCII'],
        ['sig' => '49 49 2A 00', 'offset' => 0, 'desc' => 'TIFF file_2', 'enc' => 'ASCII'],
        ['sig' => '4D 4D 00 2A', 'offset' => 0, 'desc' => 'TIFF file_3', 'enc' => 'ASCII'],
        ['sig' => '4D 4D 00 2B', 'offset' => 0, 'desc' => 'TIFF file_4', 'enc' => 'ASCII'],
    ],
    'tiff' =>[
        ['sig' => '49 20 49', 'offset' => 0, 'desc' => 'TIFF file_1', 'enc' => 'ASCII'],
        ['sig' => '49 49 2A 00', 'offset' => 0, 'desc' => 'TIFF file_2', 'enc' => 'ASCII'],
        ['sig' => '4D 4D 00 2A', 'offset' => 0, 'desc' => 'TIFF file_3', 'enc' => 'ASCII'],
        ['sig' => '4D 4D 00 2B', 'offset' => 0, 'desc' => 'TIFF file_4', 'enc' => 'ASCII'],
    ],
    'doc' =>[
        ['sig' => 'D0 CF 11 E0 A1 B1 1A E1', 'offset' => 0, 'desc' => 'Microsoft Office document', 'enc' => 'ASCII'],
        ['sig' => '0D 44 4F 43', 'offset' => 0, 'desc' => 'DeskMate Document', 'enc' => 'ASCII'],
        ['sig' => 'CF 11 E0 A1 B1 1A E1 00', 'offset' => 0, 'desc' => 'Perfect Office document', 'enc' => 'ASCII'],
        ['sig' => 'DB A5 2D 00', 'offset' => 0, 'desc' => 'Word 2.0 file', 'enc' => 'ASCII'],
        ['sig' => 'EC A5 C1 00', 'offset' => 512, 'desc' => 'Word document subheader', 'enc' => 'ASCII'],
    ],
    'docx' =>[
        ['sig' => '50 4B 03 04', 'offset' => 0, 'desc' => 'MS Office Open XML Format Document', 'enc' => 'ASCII'],
        ['sig' => '50 4B 03 04 14 00 06 00', 'offset' => 0, 'desc' => 'MS Office 2007 documents', 'enc' => 'ASCII'],
    ],
    'xls' =>[
        ['sig' => 'D0 CF 11 E0 A1 B1 1A E1', 'offset' => 0, 'desc' => 'Microsoft Office document', 'enc' => 'ASCII'],
        ['sig' => '09 08 10 00 00 06 05 00', 'offset' => 512, 'desc' => 'Excel spreadsheet subheader_1', 'enc' => 'ASCII'],
        ['sig' => 'FD FF FF FF 10', 'offset' => 512, 'desc' => 'Excel spreadsheet subheader_2', 'enc' => 'ASCII'],
        ['sig' => 'FD FF FF FF 1F', 'offset' => 512, 'desc' => 'Excel spreadsheet subheader_3', 'enc' => 'ASCII'],
        ['sig' => 'FD FF FF FF 22', 'offset' => 512, 'desc' => 'Excel spreadsheet subheader_4', 'enc' => 'ASCII'],
        ['sig' => 'FD FF FF FF 23', 'offset' => 512, 'desc' => 'Excel spreadsheet subheader_5', 'enc' => 'ASCII'],
        ['sig' => 'FD FF FF FF 28', 'offset' => 512, 'desc' => 'Excel spreadsheet subheader_6', 'enc' => 'ASCII'],
        ['sig' => 'FD FF FF FF 29', 'offset' => 512, 'desc' => 'Excel spreadsheet subheader_7', 'enc' => 'ASCII'],
    ],
    'xlsx' =>[
        ['sig' => '50 4B 03 04', 'offset' => 0, 'desc' => 'MS Office Open XML Format Document', 'enc' => 'ASCII'],
        ['sig' => '50 4B 03 04 14 00 06 00', 'offset' => 0, 'desc' => 'MS Office 2007 documents', 'enc' => 'ASCII'],
    ],
    'pdf' =>[
        ['sig' => '25 50 44 46', 'offset' => 0, 'desc' => 'PDF file', 'enc' => 'ASCII'],
    ],
    'ppg' =>[
        ['sig' => '', 'offset' => 0, 'desc' => 'ProPixel2D Image', 'enc' => 'ASCII'],//не нашёл сигнатуру
    ],
    'plt' =>[
        ['sig' => '', 'offset' => 0, 'desc' => 'AutoCAD Plotter Document', 'enc' => 'ASCII'],//не нашёл сигнатуру
    ]
];