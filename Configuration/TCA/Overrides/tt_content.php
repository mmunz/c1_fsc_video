<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

// c1_fsc_video
ExtensionManagementUtility::addPlugin(
    [
        'label' => 'LLL:EXT:c1_fsc_video/Resources/Private/Language/TCA.xlf:c1_fsc_video',
        'description' => 'LLL:EXT:c1_fsc_video/Resources/Private/Language/TCA.xlf:c1_fsc_video_description',
        'group' => 'default',
        'value' => 'c1_fsc_video',
        'icon' => 'c1_fsc_video',
    ],
    'CType',
    'c1_fsc_video',

);

$GLOBALS['TCA']['tt_content']['types']['c1_fsc_video'] = array(
    'showitem' => '
        --palette--;;general,
        --palette--;;header,
        assets,
        image;LLL:EXT:c1_fsc_video/Resources/Private/Language/TCA.xlf:c1_fsc_video_preview_image,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
',
// one video is required and maximum
    'columnsOverrides' => array(
        'assets' => array(
            'config' => array(
                'minitems' => 1,
                'maxitems' => 1,
                'allowed' => 'youtube,vimeo',
            ),
        ),
        'image' => array(
            'config' => array(
                'minitems' => 0,
                'maxitems' => 1,
                'allowed' => 'jpg,jpeg,png',
            ),
        )
    )
);
