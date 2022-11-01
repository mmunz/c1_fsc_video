<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

call_user_func(function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('c1_fsc_video', 'Configuration/TypoScript', 'FSC video content element');
    // Add page.ts
    $pageTsConfig = @file_get_contents(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('c1_fsc_video') . 'Configuration/TSConfig/page.ts');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig($pageTsConfig);
});

