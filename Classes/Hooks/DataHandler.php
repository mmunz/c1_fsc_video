<?php

namespace C1\C1FscVideo\Hooks;

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;

/**
 * Class Datamap
 *
 * @package C1\C1FscVideo\Hooks
 */
class DataHandler {

    protected $id = NULL;

    /**
     * @var string
     */
    protected $storageUid = '1';

    /**
     * @var int
     */
    protected $pid = 0;

    /**
     * @var string
     */
    protected $previewUploadFolder = '/'; // fileadmin/

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $oEmbedData = [];

    /**
     * @param $status
     * @param $table
     * @param $id
     * @param $fieldArray
     * @param $self
     */
    public function processDatamap_afterAllOperations($tceMain) {
        foreach ($tceMain->datamap as $table => $tableData) {
            if ($table === 'tt_content') {
                foreach ($tableData as $identity => $_) {
                    $this->id = $identity;
                    if (strpos($identity, 'NEW') !== FALSE) {
                        $this->id = $tceMain->substNEWwithIDs[$identity];
                    }
                }
                $this->pid = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tt_content', 'uid=' . $this->id)['pid'];
                $this->data = $tableData[$this->id];
                if ($this->id && $this->data['CType'] === 'c1_fsc_video') {
                    $files = $this->getVideos($this->id);
                    $video = $files[0];
                    // set upload storage and path for the preview to the same as for the original file
                    $this->storageUid = $video['original']['storage'];
                    $this->previewUploadFolder = dirname($video['original']['identifier']);

                    if ($video) {
                        if (!isset($this->data['image']) || trim($this->data['image']) === '' || $this->data['image'] == '0') {
                            $this->getPreviewImage($video);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the upload folder
     * Use the Signal to override the default folder
     *
     * @return string
     */
    protected function getUploadFolder() {
        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $dispatcher */
        $dispatcher = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $arguments = array(
            $this->previewUploadFolder,
        );
        $arguments = $dispatcher->dispatch(__CLASS__, __METHOD__, $arguments);
        return $arguments[0];
    }

    /**
     * Create a file reference
     *
     * @param File $file
     * @return void
     */
    protected function createFileReference($file) {
        $faldata = array();
        $falData['sys_file_reference']['NEW1234'] = array(
            'table_local' => 'sys_file',
            'uid_local' => $file->getUid(),
            'tablenames' => 'tt_content',
            'uid_foreign' => $this->id,
            'fieldname' => 'image',
            'pid' => $this->pid,
        );
        if (isset($this->oEmbedData['title'])) {
            $falData['sys_file_reference']['NEW1234']['title'] = $this->oEmbedData['title'];
            $falData['sys_file_reference']['NEW1234']['alternative'] = $this->oEmbedData['title'];
        };

        $falData['tt_content'][$this->id] = array(
            'image' => 'NEW1234' // changed automatically
        );

        /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
        $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
        $tce->start($falData, array());
        $tce->process_datamap();
        if ($tce->errorLog) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($tce->errorLog, 'Creating file reference for thumbnail failed. TCE->errorLog:');
        }
    }

    /**
     * Get preview image
     *
     * @param File $file
     * @return string
     */
    public function getPreviewImage($video) {
        if ($video['original']['extension'] === 'vimeo' || $video['original']['extension'] === 'youtube') {
            $this->onlineMediaPreviewImage($video);
        }
    }

    /**
     * Get online media preview image
     *
     * @param array $video
     * @return string
     */
    protected function onlineMediaPreviewImage($video) {
        $previewFileName = basename($video['original']['name'] . '.jpg');
        $resourceFactory = ResourceFactory::getInstance();
        $folder = $resourceFactory->retrieveFileOrFolderObject($this->storageUid . ':' . $this->getUploadFolder());
        
        if ($folder->hasFile($previewFileName)) {
            $fileName = $this->getUploadFolder() . '/' . $previewFileName;
            $storageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository'); // create instance to storage repository
            $storage = $storageRepository->findByUid(1);    // get file storage with uid 1 (this should by default point to your fileadmin/ directory)
            $file = $storage->getFile($fileName); // create file object for the image (the file will be indexed if necessary)
        } else {
            $thumbnailUrl = $this->oEmbedData['thumbnail_url'];
            // for youtube this is only 480px, use a better thumbnail
            if ($video['original']['extension'] === 'youtube') {
                $thumbnailUrl = sprintf('https://img.youtube.com/vi/%s/maxresdefault.jpg', $video['onlineMediaId']);
            }
            // Download and store image
            $thumbnailData = GeneralUtility::getUrl($thumbnailUrl);
            if ($thumbnailData) {
                $file = $folder->createFile($previewFileName);
                $file->setContents($thumbnailData);
            }
        }

        if ($file instanceof File) {
            $this->createFileReference($file);
        }
    }

    protected function getVideos() {
        // get all videos for this CE
        $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
        $fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $this->id);
        // get Imageobject information
        $files = array();

        foreach ($fileObjects as $key => $value) {
            $onlineMediaHelper = $this->getOnlineMediaHelper($value->getOriginalFile());
            if ($onlineMediaHelper) {
                $files[$key]['onlineMediaId'] = $onlineMediaHelper->getOnlineMediaId($value->getOriginalFile());
            }

            $files[$key]['reference'] = $value->getReferenceProperties();
            $files[$key]['original'] = $value->getOriginalFile()->getProperties();
            $files[$key]['videoUrl'] = $value->getPublicUrl();

            if ($files[$key]['original']['extension'] === 'vimeo' || $files[$key]['original']['extension'] === 'youtube') {
                $this->oEmbedData = $onlineMediaHelper->getOEmbed($files[$key]['onlineMediaId']);
            };
        }
        return $files;
    }

    /**
     * Get online media helper
     *
     * @param $file
     * @return bool|OnlineMediaHelperInterface
     */
    protected function getOnlineMediaHelper($file) {
        if ($this->onlineMediaHelper === null) {
            $this->onlineMediaHelper = OnlineMediaHelperRegistry::getInstance()->getOnlineMediaHelper($file);
        }
        return $this->onlineMediaHelper;
    }
}
