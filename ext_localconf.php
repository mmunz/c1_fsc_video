<?php

defined('TYPO3') or die();

// Register custom render handlers
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::class)
    ->registerRendererClass(\C1\C1FscVideo\Rendering\VimeoRenderer::class);

// Register custom youtube/vimeo helpers to get better previews
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['youtube'] = \C1\C1FscVideo\Helpers\YouTubeHelper::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['vimeo'] = \C1\C1FscVideo\Helpers\VimeoHelper::class;

// Creates the preview image of online videos when saving the content element
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['c1_fsc_video'] = \C1\C1FscVideo\Hooks\DataHandler::class;
