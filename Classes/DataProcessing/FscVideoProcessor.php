<?php

namespace C1\C1FscVideo\DataProcessing;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;

/**
 * This data processor will calculate video ratio and provide the preview
 * image and it's meta information.
 */
class FscVideoProcessor implements DataProcessorInterface {
    /**
     * @var OnlineMediaHelperInterface
     */
    protected $onlineMediaHelper;

    /**
     * Process data for the CType "fs_slider"
     *
     * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     * @throws ContentRenderingException
     */
    public function process(
    ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData
    ) {
        $processedData['video'] = array();
        $files = $processedData['files'];
        foreach ($files as $file) {
            if ($file instanceof FileReference) {
                $orgFile = $file->getOriginalFile();
            } else {
                $orgFile = $file;
            }
            // properties of the video file
            $properties = $file->getProperties();
            $processedData['video'] = $properties;
            $processedData['video']['aspectRatio'] = 56.25; // use as default

            if ($properties['extension'] == 'youtube' or $properties['extension'] == 'vimeo') {
                if ( is_numeric($properties['width']) and is_numeric($properties['height']) ) {
                    $processedData['video']['aspectRatio'] = 100 / $properties['width'] * $properties['height'];
                }
                /* id on the media portal */
                $processedData['video']['id'] = $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);
                $processedData['video']['url'] = $file->getPublicUrl();
                // get the first image
                $previewImage = $processedData['images'][0];
                $processedData['video']['preview'] = $previewImage->getProperties();
                $processedData['video']['preview']['file'] = $previewImage;
                $processedData['video']['preview']['public_url'] = $previewImage->getPublicUrl();
            }
        }
        return $processedData;
    }

    /**
     * Get online media helper
     *
     * @param FileInterface $file
     * @return bool|OnlineMediaHelperInterface
     */
    protected function getOnlineMediaHelper(FileInterface $file) {
        if ($this->onlineMediaHelper === null) {
            $orgFile = $file;
            if ($orgFile instanceof FileReference) {
                $orgFile = $orgFile->getOriginalFile();
            }
            if ($orgFile instanceof File) {
                $helperRegistry = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class);
                $this->onlineMediaHelper = $helperRegistry->getOnlineMediaHelper($orgFile);
            } else {
                $this->onlineMediaHelper = false;
            }
        }
        return $this->onlineMediaHelper;
    }
}
