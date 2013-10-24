<?php namespace Dotink\Lab
{
	use Dotink\Flourish\URL;

	return [

		'setup' => function($data){
			needs($data['root'] . '/src/URL.php');

		},

		'tests' => [

			//
			//
			//

			'Instantiation [NULL args]' => function($data)
			{
				$url1 = new Url();

				$_SERVER['SERVER_NAME'] = 'example.com';
				$url2 = new Url();

				$_SERVER['HTTP_HOST'] = 'www.example.com';
				$url3 = new Url();

				$_SERVER['HTTP_HOST'] = 'www.example.com:8080';
				$url4 = new Url();

				assert('Dotink\Flourish\URL::get')

					-> using  ($url1)
					-> equals ('http://' . gethostname() . '/')

					-> using  ($url2)
					-> equals ('http://example.com/')

					-> using  ($url3)
					-> equals ('http://www.example.com/')

					-> using  ($url4)
					-> equals ('http://www.example.com/')
				;
			},

			//
			//
			//

			'Instantiation [Simple URLs]' => function($data)
			{
				$url1 = new Url('https://www.github.com/dotink/flourish-url');
				$url2 = new Url('  http://www.google.com');

				assert('Dotink\Flourish\URL::get')
					-> using  ($url1)
					-> equals ('https://www.github.com/dotink/flourish-url')

					-> using  ($url2)
					-> equals ('http://www.google.com/')
				;
			},

			//
			//
			//

			'Instantiation [Complex URL]' => function($data)
			{
				$url = new Url('http://www.google.com/search?q=new+test&page=2#results');

				assert('Dotink\Flourish\URL::get')

					-> using  ($url)
					-> equals ('http://www.google.com/search?q=new%20test&page=2#results')

				;
			},

			//
			//
			//

			'getDomain()' => function($data)
			{
				$url1 = new Url('http://www.google.com/search?q=new+test&page=2#results');
				$url2 = new Url('http://www.google.com:80/dotink/');
				$url3 = new Url('https://www.github.com:443/dotink/flourish-url');
				$url4 = new Url('https://www.github.com:80/dotink/');

				assert('Dotink\Flourish\URL::getDomain')

					-> using  ($url1)
					-> equals ('http://www.google.com')

					-> using  ($url2)
					-> equals ('http://www.google.com')     // port removed, default for scheme

					-> using  ($url3)
					-> equals ('https://www.github.com')    // port removed, default for scheme

					-> using  ($url4)
					-> equals ('https://www.github.com:80') // port should stay, not default
				;
			},

			//
			//
			//

			'getFragment()' => function($data)
			{
				$url1 = new Url('http://www.google.com/search?q=new+test&page=2#results');
				$url2 = new Url('http://www.google.com:80/dotink/');
				$url3 = new Url();
				$url4 = new Url('/dotink#example');

				assert('Dotink\Flourish\URL::getFragment')

					-> using  ($url1)
					-> equals ('results')

					-> using  ($url2)
					-> equals (NULL)

					-> using  ($url3)
					-> equals (NULL)

					-> using  ($url4)
					-> with   (TRUE)
					-> equals ('#example')
				;
			},

			//
			//
			//

			'getHost()' => function($data)
			{
				$url1 = new Url('sftp://dotink.org');
				$url2 = new Url('http://www.google.com/search?q=test');
				$url3 = new Url('https://www.github.com');
				$url4 = new Url();

				assert('Dotink\Flourish\URL::getHost')

					-> using  ($url1)
					-> equals ('dotink.org')

					-> using  ($url2)
					-> equals ('www.google.com')

					-> using  ($url3)
					-> equals ('www.github.com')

					-> using  ($url4)
					-> equals ('www.example.com')
				;
			},

			//
			//
			//

			'getPath()' => function($data)
			{
				$url1 = new Url('sftp://dotink.org');
				$url2 = new Url('http://www.google.com/search?q=test');
				$url3 = new Url('/groups/admin');
				$url4 = new Url();

				assert('Dotink\Flourish\URL::getPath')

					-> using  ($url1)
					-> equals ('/')

					-> using  ($url2)
					-> equals ('/search')

					-> using  ($url3)
					-> equals ('/groups/admin')

					-> using  ($url4)
					-> equals ('/');
				;
			},

			//
			//
			//

			'getPathWithQuery()' => function($data)
			{
				$url1 = new Url('sftp://dotink.org');
				$url2 = new Url('http://www.google.com/search?q=test');
				$url3 = new Url('/groups/admin?filter=only+active');
				$url4 = new Url('?foo=bar');

				assert('Dotink\Flourish\URL::getPathWithQuery')

					-> using  ($url1)
					-> equals ('/')

					-> using  ($url2)
					-> equals ('/search?q=test')

					-> using  ($url3)
					-> equals ('/groups/admin?filter=only%20active')

					-> using  ($url4)
					-> equals ('/?foo=bar');
				;
			},

			//
			//
			//

			'getQuery()' => function($data)
			{
				$url1 = new Url('sftp://dotink.org');
				$url2 = new Url('http://www.google.com/search?q=test');
				$url3 = new Url('/groups/admin?filter=only+active');
				$url4 = new Url('?foo=bar');

				assert('Dotink\Flourish\URL::getQuery')

					-> using  ($url1)
					-> equals ('')

					-> using  ($url2)
					-> equals ('q=test')

					-> using  ($url3)
					-> equals ('filter=only%20active')

					-> using  ($url4)
					-> with   (TRUE)
					-> equals ('?foo=bar');
				;
			},

			//
			//
			//

			'getScheme()' => function($data)
			{
				$url1 = new Url('sftp://dotink.org');
				$url2 = new Url('http://www.google.com/search?q=test');
				$url3 = new Url('https://www.github.com/dotink');
				$url4 = new Url();

				assert('Dotink\Flourish\URL::getScheme')

					-> using  ($url1)
					-> equals ('sftp')

					-> using  ($url2)
					-> equals ('http')

					-> using  ($url3)
					-> equals ('https')

					-> using  ($url4)
					-> equals ('http');
				;
			},


			//
			//
			//

			'modify()' => function($data)
			{
				$url1 = new Url('sftp://dotink.org');
				$url2 = new Url('http://www.google.com/search?q=test');
				$url3 = new Url('https://www.github.com/dotink');
				$url4 = new Url();
				$url5 = new Url('http://www.example.com/deep/path/test');

				assert($url1->modify('/home/matts')->get())
					-> equals('sftp://dotink.org/home/matts')
				;

				assert($url1->modify(['port' => 23])->get())
					-> equals('sftp://dotink.org:23/')
				;

				assert($url2->modify(['scheme' => 'https'])->get())
					-> equals('https://www.google.com/search?q=test')
				;

				assert($url2->modify('?q=foo')->get())
					-> equals('http://www.google.com/search?q=foo')
				;

				assert($url3->modify(['host' => 'github.com'])->get())
					-> equals('https://github.com/dotink')
				;

				assert($url3->modify('../imarc/pluck')->get())
					-> equals('https://www.github.com/imarc/pluck')
				;

				assert($url3->modify('./flourish-url')->get())
					-> equals('https://www.github.com/flourish-url')
				;

				assert($url3->modify('/dotink/')->modify('./flourish-url')->get())
					-> equals('https://www.github.com/dotink/flourish-url')
				;

				assert($url4->modify(['port' => 8080]))
					-> equals('http://www.example.com:8080/')
				;

				assert($url5->modify('../newpath?param=value')->get())
					-> equals('http://www.example.com/deep/newpath?param=value')
				;
			},
		]
	];
}
