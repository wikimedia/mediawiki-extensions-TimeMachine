<?php

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'TimeMachine',
	'descriptionmsg' => 'timemachine-desc',
	'version' => '0.2.0',
	'author' => 'Luis Felipe Schenone',
	'url' => 'https://www.mediawiki.org/wiki/Extension:TimeMachine',
);

$wgMessagesDirs['TimeMachine'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['TimeMachine'] = __DIR__ . '/TimeMachine.i18n.php';
$wgExtensionMessagesFiles['TimeMachineAlias'] = __DIR__ . '/TimeMachine.alias.php';

$wgSpecialPages['TimeMachine'] = 'SpecialTimeMachine';

$wgAutoloadClasses['SpecialTimeMachine'] = __DIR__ . '/SpecialTimeMachine.php';

$wgHooks['BeforeInitialize'][] = 'SpecialTimeMachine::onBeforeInitialize';
