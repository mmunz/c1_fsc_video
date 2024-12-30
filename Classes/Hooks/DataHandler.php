<?php

namespace C1\C1FscVideo\Hooks;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Class Datamap
 *
 * @package C1\C1FscVideo\Hooks
 */
class DataHandler {

    protected $identity = NULL;

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

    protected OnlineMediaHelperRegistry $onlineMediaHelperRegistry;

    public function __construct() {
        $this->onlineMediaHelperRegistry = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class);
    }

    /**
     * @param $status
     * @param $table
     * @param $id
     * @param $fieldArray
     * @param $self
     * @throws Exception
     */
    public function processDatamap_afterAllOperations($tceMain): void
    {
        foreach ($tceMain->datamap as $table => $tableData) {
            if ($table === 'tt_content') {
                foreach ($tableData as $identity => $_) {
                    $this->identity = $identity;
                    if (strpos($identity, 'NEW') !== FALSE) {
                        $this->identity = $tceMain->substNEWwithIDs[$identity];
                    }
                }
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
                $statement = $queryBuilder
                    ->select('pid')
                    ->from('tt_content')
                    ->setMaxResults(1)->where($queryBuilder->expr()->eq('uid', $this->identity))->executeQuery();

                $row = $statement->fetchAssociative();
                $this->pid = $row['pid'] ?? 0;


                if (array_key_exists($this->identity, $tableData)) {
                    $this->data = $tableData[$this->identity];
                }

                if (is_array($this->data) && $this->identity && array_key_exists('CType', $this->data) && $this->data['CType'] === 'c1_fsc_video') {
                    $files = $this->getVideos();
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
     * Create a file reference
     *
     * @param File $file
     * @return void
     */
    protected function createFileReference($file): void
    {
        $falData['sys_file_reference']['NEW1234'] = array(
            'table_local' => 'sys_file',
            'uid_local' => $file->getUid(),
            'tablenames' => 'tt_content',
            'uid_foreign' => $this->identity,
            'fieldname' => 'image',
            'pid' => $this->pid,
        );
        if (isset($this->oEmbedData['title'])) {
            $falData['sys_file_reference']['NEW1234']['title'] = $this->oEmbedData['title'];
            $falData['sys_file_reference']['NEW1234']['alternative'] = $this->oEmbedData['title'];
        };

        $falData['tt_content'][$this->identity] = array(
            'image' => 'NEW1234' // changed automatically
        );

        /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
        $tce = GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
        $tce->start($falData, array());
        $tce->process_datamap();
        if ($tce->errorLog) {
            DebuggerUtility::var_dump($tce->errorLog, 'Creating file reference for thumbnail failed. TCE->errorLog:');
        }
    }

    public function getPreviewImage(array $video): void {
        if ($video['original']['extension'] === 'vimeo' || $video['original']['extension'] === 'youtube') {
            $this->onlineMediaPreviewImage($video);
        }
    }

    protected function onlineMediaPreviewImage(array $video): void {
        $previewFileName = basename($video['original']['name'] . '.jpg');
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $folder = $resourceFactory->retrieveFileOrFolderObject($this->storageUid . ':' . $this->previewUploadFolder);

        if ($folder->hasFile($previewFileName)) {
            $fileName = $this->previewUploadFolder . '/' . $previewFileName;
            $storageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository'); // create instance to storage repository
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
        $fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
        $fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $this->identity);
        // get Imageobject information
        $files = array();

        foreach ($fileObjects as $key => $value) {
            $onlineMediaHelper = $this->onlineMediaHelperRegistry->getOnlineMediaHelper($value->getOriginalFile());
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
}
