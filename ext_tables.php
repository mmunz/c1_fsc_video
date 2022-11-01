<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['c1_fsc_video'] = 'C1\\C1FscVideo\\Hooks\\DataHandler';
