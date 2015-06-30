<?php
# Alert the user that this is not a valid access point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/DebugTemplates/DebugTemplates.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'DebugTemplates',
	'author' => 'Clark Verbrugge',
	'license-name' => 'CC BY-SA 3.0',
	'url' => '',
	'descriptionmsg' => 'debugtemplates-desc',
	'version' => '0.5',
);

$wgAutoloadClasses['SpecialDebugTemplates'] = __DIR__ . '/SpecialDebugTemplates.php';
$wgAutoloadClasses['ApiDebugTemplates'] = __DIR__ . '/ApiDebugTemplates.php';

$wgMessagesDirs['DebugTemplates'] = __DIR__ . "/i18n";
$wgExtensionMessagesFiles['DebugTemplatesAlias'] = __DIR__ . '/DebugTemplates.alias.php';

$wgSpecialPages['DebugTemplates'] = 'SpecialDebugTemplates';
$wgAPIModules['expandframe'] = 'ApiDebugTemplates';

$wgResourceModules['ext.debugTemplates'] = array(
	'scripts' => array( 'ext.debugTemplates.js' ),
	'styles' => 'ext.debugTemplates.css',

	// error and warning messages used in the javascript
	'messages' => array( 'debugtemplates-error-parse',
		'debugtemplates-error-button',
		'debugtemplates-error-eval',
		'debugtemplates-error-arg-eval',
		'debugtemplates-warning-template-not-a-template',
		'debugtemplates-warning-template-not-found',
		'debugtemplates-error-template-name',
		'debugtemplates-error-template-revisions',
		'debugtemplates-error-template-page',
		'debugtemplates-args-constructed',
		'debugtemplates-args-eval-all' ),

	// no dependencies
	'dependencies' => array(  ),

	'localBasePath' => __DIR__,
	'remoteExtPath' => 'DebugTemplates'
);
