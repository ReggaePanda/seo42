<?php

global $REX;

if (isset($REX['TABLE_PREFIX'])) {
	$tablePrefix = $REX['TABLE_PREFIX'];
} else {
	$tablePrefix = 'rex_';
}

// redirects extras (SEO42 4.2.0+)
$sql = new rex_sql();
$sql->setQuery('ALTER TABLE `' . $tablePrefix . 'redirects` ADD `create_date` DATETIME, ADD `expire_date` DATETIME');

// if one day seo42_update_msg value will be changed, key name + update.inc.php must be changed too, otherwise user will get old msg!
if ($I18N->hasMsg('seo42_update_msg')) {
	$msg = $I18N->msg('seo42_update_msg');
} else {
	$msg = 'SEO42: Bitte beachten Sie die <a href="index.php?page=seo42&subpage=help&chapter=update">Update-Hinweise</a> für diese Version (wenn vorhanden).';
}

echo rex_info($msg);

$REX['ADDON']['update']['seo42'] = 1;
