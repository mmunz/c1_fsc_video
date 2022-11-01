<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'c1_fsc_video',
    'Configuration/TypoScript',
    'FSC video content element'
);


