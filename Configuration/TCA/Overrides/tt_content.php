<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// c1_fsc_video
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content', 'CType', [
        'LLL:EXT:c1_fsc_video/Resources/Private/Language/TCA.xlf:c1_fsc_video',
        'c1_fsc_video',
        'c1_fsc_video'
    ], 'html', 'after'
);

$GLOBALS['TCA']['tt_content']['types']['c1_fsc_video'] = array(
    'showitem' => '
        --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general,
        --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.header;header,
        assets,
        image;LLL:EXT:c1_fsc_video/Resources/Private/Language/TCA.xlf:c1_fsc_video_preview_image,
     --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
        --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.visibility;visibility,
        --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.access;access,
     --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended
',
// one video is required and maximum
    'columnsOverrides' => array(
        'assets' => array(
            'config' => array(
                'minitems' => 1,
                'maxitems' => 1,
                'foreign_selector_fieldTcaOverride' => array(
                    'config' => array(
                        'appearance' => array(
                            'elementBrowserType' => 'file',
                            'elementBrowserAllowed' => 'youtube,vimeo'
                        )
                    )
                )
            ),
        ),
        'image' => array(
            'config' => array(
                'minitems' => 0,
                'maxitems' => 1,
                'foreign_selector_fieldTcaOverride' => array(
                    'config' => array(
                        'appearance' => array(
                            'elementBrowserType' => 'file',
                            'elementBrowserAllowed' => 'jpg,jpeg,png'
                        )
                    )
                )
            ),
        )
    )
);
?>
