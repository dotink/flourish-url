<?php namespace Dotink\Flourish
{
	/**
	 * Provides functionality to prase and manipulate URLs
	 *
	 * This class uses `$_SERVER['REQUEST_URI']` for the default URL, meaning that the original URL
	 * entered by the user will be used, or that any rewrites will **not** be reflected by this
	 * class.
	 *
	 * @copyright  Copyright (c) 2007-2011 Will Bond, others
	 * @author     Will Bond           [wb]  <will@flourishlib.com>
	 * @author     Matthew J. Sahagian [mjs] <msahagian@dotink.org>
	 *
	 * @license    http://flourishlib.com/license
	 *
	 * @package    Dotink\Flourish
	 */
	class URL
	{
		static private $defaultPorts = array(
			'ftp'   => 21,
			'sftp'  => 22,
			'ssh'   => 22,
			'smtp'  => 25,
			'http'  => 80,
			'https' => 443,
			'pop3'  => 110,
			'imap'  => 143
		);


		/**
		 * Constructs a new URL object
		 *
		 * Any parts of the URL not specified in the original argument will be initialized
		 * from the current request.
		 *
		 * @access public
		 * @param string $url The URL represented as a string
		 * @return void
		 */
		public function __construct($url = NULL)
		{
			$scheme = NULL;

			if (isset($_SERVER['SERVER_PROTOCOL'])) {
				$split  = strpos($_SERVER['SERVER_PROTOCOL'], '/');
				$scheme = $split !== FALSE
					? strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, $split))
					: strtolower($_SERVER['SERVER_PROTOCOL']);

				if ($scheme == 'http' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
					$scheme = 'https';
				}

			}

			$this->url = array_merge(
				array(

					//
					// Default values are pulled from the current request
					//

					'scheme' => isset($scheme)
						? $scheme
						: 'http',

					'host' => isset($_SERVER['SERVER_NAME'])
						? $_SERVER['SERVER_NAME']
						: gethostname(),

					'port' => isset($_SERVER['SERVER_PORT'])
						? $_SERVER['SERVER_PORT']
						: NULL,

					'path' => isset($_SERVER['REQUEST_URI'])
						? preg_replace('#\?.*$#D', '', $_SERVER['REQUEST_URI'])
						: '/',

					'query' => isset($_SERVER['REQUEST_URI'])
						? preg_replace('#^[^?]*\??#', '', $_SERVER['REQUEST_URI'])
						: '',

					'fragment' => NULL
				),
				parse_url($url)
			);

			if ($this->url['query']) {
				parse_str($this->url['query'], $this->url['query']);
			}

			$this->normalizePort();
		}


		/**
		 * Converts the URl to a string
		 *
		 * @return string The full URL
		 */
		public function __toString()
		{
			return $this->get();
		}


		/**
		 * Get the full URL
		 *
		 * @return string The full URL
		 */
		public function get()
		{
			return $this->getDomain() . $this->getPathWithQuery() . $this->getFragment(TRUE);
		}


		/**
		 * Get the domain name, with protcol prefix and non-default port.
		 *
		 * Port will be included if not 80 for HTTP or 443 for HTTPS.
		 *
		 * @return string The current domain name, prefixed by `http://` or `https://`
		 */
		public function getDomain()
		{
			$port = $this->url['port']
				? ':' . $this->url['port']
				: NULL;

			return $this->url['scheme'] . '://' . $this->url['host'] . $port;
		}


		/**
		 * Get the fragment
		 *
		 * @param boolean $include_hash Whether or not to prepend the #, default: FALSE
		 * @return string The fragment, optionally prepended with #
		 */
		public function getFragment($include_hash = FALSE)
		{
			return $this->url['fragment']
				? ($include_hash ? '#' : NULL) . $this->url['fragment']
				: NULL;
		}


		/**
		 * Returns the url host
		 *
		 * @return string The URL host
		 */
		public function getHost()
		{
			return $this->url['host'];
		}


		/**
		 * Returns the url path
		 *
		 * @return string  The URL path
		 */
		public function getPath()
		{
			return $this->url['path'];
		}


		/**
		 * Returns the url path with the query string
		 *
		 * @return string  The URL with query string
		 */
		public function getPathWithQuery()
		{
			return $this->url['path'] . $this->getQuery(TRUE);
		}


		/**
		 * Get the query string, does not include parameters added by rewrites
		 *
		 * @param boolean $include_question_mark Whether or not to prepend the ?, default: FALSE
		 * @return string The query string, optionally prepended with ?
		 */
		public function getQuery($include_question_mark = FALSE)
		{
			return ($query = http_build_query($this->url['query'], NULL, '&', PHP_QUERY_RFC3986))
				? ($include_question_mark ? '?' : NULL) . $query
				: NULL;
		}


		/**
		 * Get a new URL object, modified from URL using a normalized string or associative array
		 *
		 * Passing a string will replace or modify components depending on where that location
		 * starts.  Using an array allows you to replace selective pieces.
		 *
		 * If the location...
		 *
		 *  - starts with `/`, it is treated as an absolute path
		 *  - starts with a scheme (e.g. `http://`), it is treated as a fully-qualified URL
		 *  - starts with ./ it is treated as a relative path
		 *  - starts with # it is treated as a fragment additon or replacement
		 *  - is ommitted, it is treated as the current URL
		 *
		 * If the location is an array, it will replace parts of the URL identified by the keys
		 * with the value of those keys.
		 *
		 * @access public
		 * @param string|array $location The location to modify with
		 * @return URL The modified URL
		 */
		public function modify($location)
		{
			$new = clone $this;

			if ($location) {
				if (is_array($location)) {
					$new->url = array_merge($new->url, $location);

					if (!is_array($new->url['query'])) {
						parse_str($new->url['query'], $new->url['query']);
					}
				} elseif (preg_match('#^[A-Za-z]+://#i', $location)) {
					$location = new self($location);

				} else {
					if (strpos($location, '/') === 0) {
						$location = new self($location);
						$new->url = array_merge(
							$new->url,
							array(
								'path'     => $location->url['path'],
								'query'    => $location->url['query'],
								'fragment' => $location->url['fragment']
							)
						);

					} elseif (strpos($location, '?') === 0) {
						$location = new self($location);
						$new->url = array_merge(
							$new->url,
							array(
								'query'    => $location->url['query'],
								'fragment' => $location->url['fragment']
							)
						);

					} elseif (strpos($location, '#') === 0) {
						$new->url['fragment'] = substr($location, 1);

					} else {
						$location  = new self($location);
						$new_path  = rtrim($new->url['path'], '/') . '/' . $location->url['path'];
						$new->url = array_merge(
							$new->url,
							array(
								'path'     => $new_path,
								'query'    => $location->url['query'],
								'fragment' => $location->url['fragment']
							)
						);
					}
				}
			}

			//
			// Some Cleanup
			//

			$new->url['path'] = str_replace('/./', '/', $new->url['path']);
			$new->url['path'] = preg_replace('#/[^/]*/\\.\\./#', '/', $new->url['path']);
			$new->url['path'] = '/' . ltrim($new->url['path'], '/');

			if (!is_array($new->url['query'])) {
				$new->url['query'] = array();
			}

			$new->normalizePort();

			return $new;
		}


		/**
		 * Removes one or more parameters from the query string
		 *
		 * @param string $parameter A parameter to remove from the query string
		 * @param string ...
		 * @return URL A new URL with the parameter(s) removed from the query
		 */
		public function removeFromQuery($parameter)
		{
			$new        = clone $this;
			$parameters = func_get_args();

			foreach ($parameters as $parameter) {
				if (isset($new->url['query'][$parameter])) {
					unset($new->url['query'][$parameter]);
				}
			}

			return $new;
		}


		/**
		 * Replaces a value in the query string
		 *
		 * @param string|array $parameter The query string parameter
		 * @param string|array $value The value to set the parameter to
		 * @return URL A new URL with the parameter(s) replaced
		 */
		public function replaceInQuery($parameter, $value = NULL)
		{
			if (func_num_args() == 1) {
				$data = $parameter;
				settype($data, 'array');
			} else {
				$data = array($parameter => $value);
			}

			$new = clone $this;


			foreach ($data as $parameter => $value) {
				$new->url['query'][$parameter] = $value;
			}

			return $new;
		}

		/**
		 * Normalizes the URL port depending on scheme
		 *
		 * @access private
		 * @return void
		 */
		private function normalizePort()
		{
			$scheme = strtolower($this->url['scheme']);

			if ($this->url['port'] && isset(self::$defaultPorts[$scheme])) {
				if ($this->url['port'] == self::$defaultPorts[$scheme]) {
					$this->url['port'] = NULL;
				}
			}
		}
	}
}
