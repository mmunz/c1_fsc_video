<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Register Icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
        'c1_fsc_video', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, array(
            'source' => 'EXT:c1_fsc_video/Resources/Public/Icons/mimetypes-media-video.svg'
        )
);

// Register custom youtube/vimeo helpers to get better previews
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['youtube'] = C1\C1FscVideo\Helpers\YouTubeHelper::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['vimeo'] = C1\C1FscVideo\Helpers\VimeoHelper::class;

// register custom render handlers
/** @var \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry $rendererRegistry */
$rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
$rendererRegistry->registerRendererClass(C1\C1FscVideo\Rendering\YouTubeRenderer::class);
$rendererRegistry->registerRendererClass(C1\C1FscVideo\Rendering\VimeoRenderer::class);
unset($rendererRegistry);
?>
