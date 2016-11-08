<?php

$config = [

	'Sphinx' => [
		'default' => '10.10.29.234:9312',
//		'default' => '10.10.29.234:9322',
		'9first_paper' => '10.10.29.234:9332',
	],

	'memcache' => [
		'default' => '10.10.29.234:11211',
		'api' => '10.10.29.234:11211',
	],

	'Beanstalk' => [
		'default' => '10.10.29.234:11311',
		'task' => '10.10.29.234:11311',
	],

	'FastDFS' => [
		'lms' => '10.10.29.234:23000',
		'paper' => '10.10.29.234:23001',
		'9first' => '10.10.29.234:23001',
	],

	'MeadiniOSPush' => [
		'certDir' => '__DIR__/push_cert',
		'cert' => 'meadin_dev.pem',
		'passphrase' => 'meadin',
		'pushUrl' => 'ssl://gateway.sandbox.push.apple.com:2195',
	],

	'VeryeastiOSPush' => [
		'certDir' => '__DIR__/push_cert',
		'cert' => 'veryeast_dev.pem',
		'passphrase' => 'veryeast',
		'pushUrl' => 'ssl://gateway.sandbox.push.apple.com:2195',
	],

	'LmsiOSPush' => [
		'certDir' => '__DIR__/push_cert',
		'cert' => 'lms_dev.pem',
		'passphrase' => '9first.com',
		'pushUrl' => 'ssl://gateway.sandbox.push.apple.com:2195',
	],

	'MeadinAndroidPush' => [
		'app_id' => 's2CsPGkMIP9TQA2MCQ6JW',
		'app_key' => 'czrOc9Yik495ECHTAbsvpA',
		'master_secret' => 'QfGmtNYXxi92Mb9nbl4UG',
		'host' => 'http://sdk.open.api.igexin.com/apiex.htm',
		'logo' => 'ic_launcher.png',
	],

	'VeryeastAndroidPush' => [
		'app_id' => 'cawc78oFPA7idbknUA5d61',
		'app_key' => '5y9DflB2vk9ruwjBPxZhT7',
		'master_secret' => 'fFaicMkW3k5cnLgEhgvX93',
		'host' => 'http://sdk.open.api.igexin.com/apiex.htm',
		'logo' => 'ic_launcher.png',
	],

	'LmsAndroidPush' => [
		'app_id' => 'eYDUP6m28v81jRmcIgF0q3',
		'app_key' => 'GYkJuYv9Dv7XukxjuMYU71',
		'master_secret' => 'DKWPFP5qLA9GGYpZgvoIS7',
		'host' => 'http://sdk.open.api.igexin.com/apiex.htm',
		'logo' => 'ic_launcher.png',
	],

	'LmsAndroidPush' => [
		'app_id' => 'eYDUP6m28v81jRmcIgF0q3',
		'app_key' => 'GYkJuYv9Dv7XukxjuMYU71',
		'master_secret' => 'DKWPFP5qLA9GGYpZgvoIS7',
		'host' => 'http://sdk.open.api.igexin.com/apiex.htm',
		'logo' => 'ic_launcher.png',
	],

	'EventUids' => [
		'enable' => true,
		'cid[]' => [76],
		'days' => 50
	],

];


return $config;