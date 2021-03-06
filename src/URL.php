<?php namespace Dotink\Flourish
{
	/**
	 * Provides functionality to manipulate URL information
	 *
	 * This class uses `$_SERVER['REQUEST_URI']` for the default URL, meaning that the original URL
	 * entered by the user will be used, or that any rewrites will **not** be reflected by this
	 * class.
	 *
	 *
	 * @copyright Copyright (c) 2007-2015 Will Bond, Matthew J. Sahagian, others
	 * @author Will Bond [wb] <will@flourishlib.com>
	 * @author Matthew J. Sahagian [mjs] <msahagian@dotink.org>
	 *
	 * @license Please reference the LICENSE.md file at the root of this distribution
	 *
	 * @package Flourish
	 */
	class URL
	{
		/**
		 * Default ports for various schemes
		 *
		 * @static
		 * @access protected
		 * @var array
		 */
		static protected $defaultPorts = array(
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
		 * The URL data
		 *
		 * @access protected
		 * @var array
		 */
		protected $data = array();


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
			$host   = gethostname();

			if (isset($_SERVER['SERVER_PROTOCOL'])) {
				$split  = strpos($_SERVER['SERVER_PROTOCOL'], '/');
				$scheme = $split !== FALSE
					? strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, $split))
					: strtolower($_SERVER['SERVER_PROTOCOL']);

				$https = ($scheme == 'http' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'])
					? $_SERVER['HTTPS'] !== 'off'
					: FALSE;

				if ($https) {
					$scheme = 'https';
				}
			}

			$this->data = array(

				//
				// Default values are pulled from the current request
				//

				'scheme' => isset($scheme)
					? $scheme
					: 'http',

				'host' => !isset($_SERVER['HTTP_HOST'])
					? (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $host)
					: preg_replace('#\:\d+$#', '', $_SERVER['HTTP_HOST']),

				'port' => isset($_SERVER['SERVER_PORT'])
					? $_SERVER['SERVER_PORT']
					: NULL,

				'path' => isset($_SERVER['REQUEST_URI'])
					? preg_replace('#\?.*$#D', '', $_SERVER['REQUEST_URI'])
					: '/',

				'query' => isset($_SERVER['REQUEST_URI'])
					? preg_replace('#^[^?]*\??#', '', $_SERVER['REQUEST_URI'])
					: '',

				'user'     => NULL,
				'pass'     => NULL,
				'fragment' => NULL
			);

			if ($url) {
				if ($url instanceof self) {
					$this->data = $url->data;
					return;

				} else {
					$url_parts = parse_url(ltrim($url));

					//
					// There are two instances where we do not want to use the current values
					// of our server.  Firstly, if a host was provided in the URL, but no port
					// was specified, we don't want our port.  If a path was provided, but no
					// query, we don't want our query.
					//

					if (isset($url_parts['host']) && !isset($url_parts['port'])) {
						$this->data['port'] = NULL;
					}

					if (isset($url_parts['path']) && !isset($url_parts['query'])) {
						$this->data['query'] = NULL;
					}

					//
					// Once we have cleared these from our defaults, we can then merge the parsed
					// values from the provided URL over our defaults.
					//

					$this->data = array_merge($this->data, $url_parts);
				}
			}

			$this->normalizePath();
			$this->normalizePort();
			$this->normalizeQuery();
		}


		/**
		 * Converts the URL to a string
		 *
		 * @access public
		 * @return string The full URL
		 */
		public function __toString()
		{
			return $this->get();
		}


		/**
		 * Get the full URL
		 *
		 * @access public
		 * @return string The full URL
		 */
		public function get()
		{
			return $this->getDomain() . $this->getPathWithQuery() . $this->getFragment(TRUE);
		}


		/**
		 *
		 */
		public function getAuthority()
		{
			return $this->getHost(TRUE) . ($this->getPort() ? ':' . $this->getPort() : NULL);
		}


		/**
		 * Get the domain name, with protcol prefix and non-default port.
		 *
		 * Port will be included if not 80 for HTTP or 443 for HTTPS.
		 *
		 * @access public
		 * @return string The current domain name, prefixed by `http://` or `https://`
		 */
		public function getDomain($include_scheme = TRUE)
		{
			return ($include_scheme ? $this->getScheme(TRUE) : '//') . $this->getAuthority();
		}


		/**
		 * Get the fragment in the URL
		 *
		 * @access public
		 * @param boolean $include_hash Whether or not to prepend the #, default: FALSE
		 * @return string The fragment, optionally prepended with #
		 */
		public function getFragment($include_hash = FALSE)
		{
			return $this->data['fragment']
				? ($include_hash ? '#' : '') . $this->data['fragment']
				: '';
		}


		/**
		 * Get the host in the URL
		 *
		 * @access public
		 * @return string  The host in the URL
		 */
		public function getHost($include_auth = FALSE)
		{
			return ($include_auth ? $this->getUserInfo(TRUE) : NULL) . $this->data['host'];
		}


		/**
		 *
		 */
		public function getPass()
		{
			return $this->data['pass'];
		}


		/**
		 * Gets the path in the URL
		 *
		 * @access public
		 * @return string The path in the URL
		 */
		public function getPath($include_query = FALSE)
		{
			return $this->data['path'] . ($include_query ? $this->getQuery(TRUE) : NULL);
		}


		/**
		 * Returns the url path with the query string
		 *
		 * @access public
		 * @return string The URL with query string
		 */
		public function getPathWithQuery()
		{
			return $this->getPath(TRUE);
		}


		/**
		 * Get the port of the url
		 *
		 * @access public
		 * @return string The port of the URL
		 */
		public function getPort()
		{
			return $this->data['port'];
		}


		/**
		 * Get the query string, does not include parameters added by rewrites
		 *
		 * @access public
		 * @param boolean $include_question_mark Whether or not to prepend the ?, default: FALSE
		 * @return string The query string, optionally prepended with ?
		 */
		public function getQuery($include_question_mark = FALSE)
		{
			if (!count($this->data['query'])) {
				return NULL;
			}

			if (!defined('PHP_QUERY_RFC3986')) {

				//
				// PHP < 5.4 did not have PHP_QUERY_RFC3986
				//

				$encoded_params = array();

				foreach ($this->data['query'] as $param => $value) {
					$encoded_params[] = rawurlencode($param) . '=' . rawurlencode($value);
				}

				$query = implode('&', $encoded_params);

			} else {
				$query = http_build_query($this->data['query'], NULL, '&', PHP_QUERY_RFC3986);
			}

			return ($include_question_mark ? '?' : NULL) . $query;
		}


		/**
		 * Gets the scheme in the URL
		 *
		 * @access public
		 * @return string The scheme in the URL
		 */
		public function getScheme($include_root = FALSE)
		{
			if (!isset($this->data['scheme'])) {
				return $include_root ? '//' : '';
			}

			$scheme  = $this->data['scheme'];
			$scheme .= $include_root ? '://' : '';

			return $scheme;
		}


		/*
		 * Get the user info from the URL
		 *
		 * @access public
		 * @return string The user info from the URL
		 */
		public function getUserInfo($include_at = FALSE)
		{
			if (!$this->getUser()) {
				return NULL;
			}

			$user_info  = $this->getUser();
			$user_info .= $this->getPass() ? (':' . $this->getPass()) : NULL;
			$user_info .= $include_at ? '@' : NULL;

			return  $user_info;
		}


		/**
		 * Get the user from the URL
		 *
		 * @access public
		 * @return string The user from the URL
		 */
		public function getUser()
		{
			return $this->data['user'];
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
		 *  - starts with `//` it is treated as a new url on the same scheme
		 *  - starts with a scheme (e.g. `http://`), it is treated as a fully-qualified URL
		 *  - starts with ./ it is treated as a relative path
		 *  - starts with # it is treated as a fragment additon or replacement
		 *
		 * If the location is an array, it will replace parts of the URL identified by the keys
		 * with the value of those keys.
		 *
		 * @example url/modify/with_array.php
		 * @example url/modify/with_string_1.php
		 * @example url/modify/with_string_2.php
		 *
		 * @access public
		 * @param string|array $location The location to modify with
		 * @return URL The modified URL
		 */
		public function modify($location)
		{
			if ($location instanceof self) {
				return clone $location;
			}

			$new = clone $this;

			if (is_array($location)) {
				if (isset($location['scheme']) && !isset($location['port'])) {
					$location['port'] = NULL;
				}

				$new->data = array_merge($new->data, $location);

			} else {
				$location = ltrim((string) $location);

				if (strpos($location, '//') === 0) {
					$location = $this->data['scheme'] . ':' . $location;
				}

				if (strpos($location, '#') !== 0) {
					$new->data['query'] = array();
				}

				$url_parts = parse_url($location);
				$new->data = array_merge(
					$new->data,
					$url_parts
				);

				if ($new->data['path'][0] != '/') {
					if (substr($this->data['path'], -1) == '/') {
						$new->data['path'] = $this->data['path'] . $new->data['path'];

					} else {
						$path_parts        = explode('/', $this->data['path']);
						$base_path         = implode('/', array_slice($path_parts, 0, -1));
						$new->data['path'] = $base_path . '/' . $new->data['path'];
					}
				}
			}

			$new->normalizePath();
			$new->normalizePort();
			$new->normalizeQuery();
			$new->normalizeScheme();
			$new->normalizeFragment();

			return $new;
		}


		/**
		 * Removes one or more parameters from the query string
		 *
		 * @access public
		 * @param string $parameter A parameter to remove from the query string
		 * @param string ...
		 * @return URL A new URL with the parameter(s) removed from the query
		 */
		public function removeFromQuery($parameter)
		{
			$new        = clone $this;
			$parameters = func_get_args();

			foreach ($parameters as $parameter) {
				if (isset($new->data['query'][$parameter])) {
					unset($new->data['query'][$parameter]);
				}
			}

			return $new;
		}


		/**
		 * Replaces a value in the query string
		 *
		 * @access public
		 * @param string|array $parameter The query string parameter
		 * @param string|array $value The value to set the parameter to
		 * @return URL A new URL with the parameter(s) replaced
		 */
		public function replaceInQuery($parameter, $value = NULL)
		{
			if (func_num_args() == 1) {
				if (!is_array($parameter)) {
					throw new ProgrammerException(
						'Single argument supplied of type %s, must be an array',
						gettype($parameter)
					);
				}

				$data = $parameter;

			} else {
				$data = array($parameter => $value);
			}

			$new = clone $this;

			foreach ($data as $parameter => $value) {
				$new->data['query'][$parameter] = $value;
			}

			return $new;
		}


		/**
		 *
		 */
		protected function normalizeFragment()
		{
			if ($this->data['fragment']) {
				$this->data['fragment'] = rawurldecode($this->data['fragment']);
			}
		}


		/**
		 * Normalizes the URL
		 *
		 * This will consolidate multiple slashes in a row, reduce back path segments (`..`) and
		 * remove current path segments (`.`).
		 *
		 * @access protected
		 * @return void
		 */
		protected function normalizePath()
		{
			$this->data['path'] = preg_replace('#/+#',   '/', $this->data['path']);
			$this->data['path'] = preg_replace('#/\./#', '/', $this->data['path']);

			while (preg_match('#/[^/]+/\.\.(?:/|$)#', $this->data['path'], $matches)) {
				$this->data['path'] = str_replace($matches[0], '/', $this->data['path']);
			}

			$this->data['path'] = str_replace('/../', '/', $this->data['path']);
		}


		/**
		 * Normalizes the URL port depending on scheme
		 *
		 * This will remove the port if the port matches the default for the scheme.
		 *
		 * @access protected
		 * @return void
		 */
		protected function normalizePort()
		{
			$scheme = strtolower($this->data['scheme']);

			if ($this->data['port'] && isset(self::$defaultPorts[$scheme])) {
				if ($this->data['port'] == self::$defaultPorts[$scheme]) {
					$this->data['port'] = NULL;
				}
			}
		}


		/**
		 *
		 */
		protected function normalizeScheme()
		{
			$this->data['scheme'] = strtolower($this->data['scheme']);
		}


		/**
		 * Normalizes the Query
		 *
		 * Parse a query set as a string or set empty arrays if invalid value is provided.
		 *
		 * @access protected
		 * @return void
		 */
		protected function normalizeQuery()
		{
			if (!is_array($this->data['query'])) {
				if (is_string($this->data['query'])) {
					$this->data['query'] = trim($this->data['query']);

					if ($this->data['query']) {
						parse_str($this->data['query'], $this->data['query']);
					} else {
						$this->data['query'] = array();
					}

				} else {
					$this->data['query'] = array();
				}
			}
		}
	}
}
