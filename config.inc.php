<?php

// register addon
$REX['ADDON']['rxid']['rexseo42'] = '0';
$REX['ADDON']['name']['rexseo42'] = 'REXSEO42';
$REX['ADDON']['version']['rexseo42'] = '1.0.42 BETA 2';
$REX['ADDON']['author']['rexseo42'] = 'Markus Staab, Wolfgang Huttegger, Dave Holloway, Jan Kristinus, jdlx, RexDude';
$REX['ADDON']['supportpage']['rexseo42'] = 'forum.redaxo.de';
$REX['ADDON']['perm']['rexseo42'] = 'rexseo42[]';

// permissions
$REX['PERM'][] = 'rexseo42[]';
$REX['EXTPERM'][] = 'rexseo42[seopage]';
$REX['EXTPERM'][] = 'rexseo42[seopage_extended]';

// includes
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/classes/class.rexseo42.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/classes/class.rexseo42_utils.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/settings.dyn.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/settings.advanced.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/settings.lang.inc.php');

// init
if (!$REX['SETUP']) {
	rex_register_extension('ADDONS_INCLUDED','rexseo42_utils::init', '', REX_EXTENSION_EARLY);
}

if ($REX['REDAXO']) {
	// append lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/rexseo42/lang/');

	// generate pathlist on each extension point
	$extensionPoints = array(
		'CAT_ADDED',     'CAT_UPDATED',     'CAT_DELETED',
		'ART_ADDED',     'ART_UPDATED',     'ART_DELETED',        'ART_META_FORM_SECTION',
		'ART_TO_CAT',    'CAT_TO_ART',      'ART_TO_STARTPAGE',
		'CLANG_ADDED',   'CLANG_UPDATED',   'CLANG_DELETED',
		'ALL_GENERATED'
	);

	foreach($extensionPoints as $extensionPoint) {
		rex_register_extension($extensionPoint, 'rexseo_generate_pathlist');
	}

	// subpages
	$REX['ADDON']['rexseo42']['SUBPAGES'] = array(
		array('', $I18N->msg('rexseo42_settings')),
		array('tools', $I18N->msg('rexseo42_tools')),
		array('setup', $I18N->msg('rexseo42_setup')),
		array('help', $I18N->msg('rexseo42_help'))
	);

	// check for user permissions (admins will have all)
	if (isset($REX['USER']) && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm('rexseo42[seopage]') || $REX['USER']->hasPerm('rexseo42[seopage_extended]') || $REX['USER']->hasPerm('editContentOnly[]'))) {
		// react on one_page_mode option
		if (!$REX['ADDON']['rexseo42']['settings']['one_page_mode'] || ($REX['ADDON']['rexseo42']['settings']['one_page_mode'] && $REX['ARTICLE_ID'] == $REX['START_ARTICLE_ID'])) {
			// add new menu item
			rex_register_extension('PAGE_CONTENT_MENU', 'rexseo42_utils::addSEOPageToPageContentMenu');

			// include seo page
			rex_register_extension('PAGE_CONTENT_OUTPUT', 'rexseo42_utils::addSEOPageToPageContentOutput');
		}
	}

	// for one page mode link to frontend is always "../"
	if ($REX['ADDON']['rexseo42']['settings']['one_page_mode'] && $REX['ARTICLE_ID'] != $REX['START_ARTICLE_ID']) {
		rex_register_extension('PAGE_CONTENT_MENU', 'rexseo42_utils::modifyFrontendLinkInPageContentMenu');
	}

	// check for missing db field after db import
	if (!$REX['SETUP']) {
		rex_register_extension('A1_AFTER_DB_IMPORT', 'rexseo42_utils::afterDBImport');
	}

	// if clang is added/deleted show message to the user that he should check his lang settings
	rex_register_extension('CLANG_ADDED', 'rexseo42_utils::showMsgAfterClangModified');
	rex_register_extension('CLANG_DELETED', 'rexseo42_utils::showMsgAfterClangModified');
}

