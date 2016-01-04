<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Video Content Element',
	'description' => 'A responsive video element.',
	'author' => 'Manuel Munz',
	'author_email' => 't3dev@comuno.net',
    'author_company' => 'comuno.net',
    'version' => '0.0.1',
	'category' => 'plugin',
    'state' => 'beta',
	'constraints' => array(
		'depends' => array(
			'core' => '7.5.0-7.6.99',
            'fluid_styled_content' => '7.5.0-7.6.99'
		),
	),
);

?>
