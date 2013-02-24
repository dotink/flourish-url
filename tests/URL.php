<?php namespace Dotink\Lab
{
	use Dotink\Flourish\URL;

	return [

		'setup' => function($data){
			needs($data['root'] . '/src/Url.php');

		},

		'tests' => [

			'Construct with NULL' => function($data)
			{
				$url = new URL();

				assert('Dotink\Flourish\URL::get')
					-> using($url)
					-> equals('http://' . gethostname());
			}
		]
	];
}
