<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'EventIndex' => 'list,countEvents,show',
		'Event' => 'show',
		'Category' => 'show',
	),
	array(
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi2',
	array(
		'EventAdministration' => 'list,new,create,edit,update,delete',
	),
	array(
		'EventAdministration' => 'list,new,create,edit,update,delete',
	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_CzSimpleCal_Scheduler_Index'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang_mod.xml:tx_czsimplecal_scheduler_index.label',
    'description'      => 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang_mod.xml:tx_czsimplecal_scheduler_index.description',
	'additionalFields' => 'tx_czsimplecal_scheduler_index'
);

// add default pageTSConfig
t3lib_extMgm::addPageTSConfig(
	file_get_contents(
		t3lib_extMgm::extPath('cz_simple_cal').'Configuration/TSconfig/default.txt'
	)
);

// Register the hook that filters inline addresses from the record list.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][] = 'Tx\\CzSimpleCal\\Hook\\DatabaseRecordListHook';

// hook into the post storing process to update the index of recurring events
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:'.$_EXTKEY.'/Legacy/class.tx_czsimplecal_getDatamapHook.php:tx_czsimplecal_getDatamapHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:'.$_EXTKEY.'/Legacy/class.tx_czsimplecal_getCmdmapHook.php:tx_czsimplecal_getCmdmapHook';

?>