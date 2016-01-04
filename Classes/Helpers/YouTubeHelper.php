<?php

namespace C1\C1FscVideo\Helpers;

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

/**
 * Extend YouTubeHelper to get oEmbedData
 */
class YouTubeHelper extends \TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper {

    /**
     * Get oEmbed Data
     *
     * @param $videoId
     * @return array
     */
    public function getOEmbed($videoId) {
        $oEmbedData = $this->getOEmbedData($videoId);
        return $oEmbedData;
    }

}
