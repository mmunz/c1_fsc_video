<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Video Content Element',
	'description' => 'A responsive video element.',
	'author' => 'Manuel Munz',
	'author_email' => 't3dev@comuno.net',
    'author_company' => 'comuno.net',
    'version' => '0.1.0',
	'category' => 'plugin',
    'state' => 'beta',
	'constraints' => array(
		'depends' => array(
			'core' => '8.6.0-8.99.99'
		),
	),
    'autoload' => [
        'psr-4' => [
            'C1\\C1FscVideo\\' => 'Classes',
        ]
    ]
);

?>
