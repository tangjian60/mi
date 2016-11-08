<?php

$config = [
	'person' 	=> ['class' => 'communal\components\veryeast\Person'],
    'job' 		=> ['class' => 'communal\components\veryeast\Job'],
    'message' 	=> ['class' => 'communal\components\veryeast\Message'],
    'weixinApi' => ['class' => 'communal\components\resource\WeixinApi'],
	'sms' 		=> ['class' => 'communal\components\resource\Sms'],
	'ad' 		=> ['class' => 'communal\components\resource\Ad'],
	'sphinx' 	=> ['class' => 'communal\components\resource\SphinxClient'],
	'resume' => ['class' => 'communal\components\veryeast\Resume'],
	'company' => ['class' => 'communal\components\veryeast\Company'],
	'area' => ['class' => 'communal\components\resource\Area'],
	'tracker' => ['class' => 'communal\components\veryeast\ResumeTracker'],
	'evaluation' => ['class' => 'communal\components\veryeast\Evaluation'],
];

return $config;