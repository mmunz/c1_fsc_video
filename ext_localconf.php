<?php

use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;

call_user_func(function () {
    // Register Icon
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'c1_fsc_video', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, array(
            'source' => 'EXT:c1_fsc_video/Resources/Public/Icons/mimetypes-media-video.svg'
        )
    );
    // register custom render handlers
    /** @var RendererRegistry $rendererRegistry */
    $rendererRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::class
    );
    $rendererRegistry->registerRendererClass(C1\C1FscVideo\Rendering\VimeoRenderer::class);
});

// Register custom youtube/vimeo helpers to get better previews
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['youtube'] = C1\C1FscVideo\Helpers\YouTubeHelper::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['vimeo'] = C1\C1FscVideo\Helpers\VimeoHelper::class;
