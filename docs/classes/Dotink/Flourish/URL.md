# URL
## Provides functionality to manipulate URL information

_Copyright (c) 2007-2011 Will Bond, others_.
_Please reference the LICENSE.md file at the root of this distribution_

### Details

This class uses `$_SERVER['REQUEST_URI']` for the default URL, meaning that the original URL
entered by the user will be used, or that any rewrites will **not** be reflected by this
class.

#### Namespace

`Dotink\Flourish`

#### Authors

<table>
	<thead>
		<th>Name</th>
		<th>Handle</th>
		<th>Email</th>
	</thead>
	<tbody>
	
		<tr>
			<td>
				Will Bond
			</td>
			<td>
				wb
			</td>
			<td>
				will@flourishlib.com
			</td>
		</tr>
	
		<tr>
			<td>
				Matthew J. Sahagian
			</td>
			<td>
				mjs
			</td>
			<td>
				msahagian@dotink.org
			</td>
		</tr>
	
	</tbody>
</table>

## Properties
### Static Properties
#### <span style="color:#6a6e3d;">$defaultPorts</span>

Default ports for various schemes



### Instance Properties
#### <span style="color:#6a6e3d;">$data</span>

The URL data




## Methods

### Instance Methods
<hr />

#### <span style="color:#3e6a6e;">__construct()</span>

Constructs a new URL object

##### Details

Any parts of the URL not specified in the original argument will be initialized
from the current request.

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td>
				$url
			</td>
			<td>
									<a href="http://php.net/language.types.string">string</a>
				
			</td>
			<td>
				The URL represented as a string
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			void
		</dt>
		<dd>
			Provides no return value.
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">__toString()</span>

Converts the URL to a string

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The full URL
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">get()</span>

Get the full URL

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The full URL
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">getDomain()</span>

Get the domain name, with protcol prefix and non-default port.

##### Details

Port will be included if not 80 for HTTP or 443 for HTTPS.

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The current domain name, prefixed by `http://` or `https://`
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">getFragment()</span>

Get the fragment in the URL

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td>
				$include_hash
			</td>
			<td>
									<a href="http://php.net/language.types.boolean">boolean</a>
				
			</td>
			<td>
				Whether or not to prepend the #, default: FALSE
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The fragment, optionally prepended with #
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">getHost()</span>

Get the host in the URL

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The host in the URL
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">getPath()</span>

Gets the path in the URL

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The path in the URL
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">getPathWithQuery()</span>

Returns the url path with the query string

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The URL with query string
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">getQuery()</span>

Get the query string, does not include parameters added by rewrites

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td>
				$include_question_mark
			</td>
			<td>
									<a href="http://php.net/language.types.boolean">boolean</a>
				
			</td>
			<td>
				Whether or not to prepend the ?, default: FALSE
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The query string, optionally prepended with ?
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">getScheme()</span>

Gets the scheme in the URL

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			The scheme in the URL
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">modify()</span>

Get a new URL object, modified from URL using a normalized string or associative array

##### Details

Passing a string will replace or modify components depending on where that location
starts.  Using an array allows you to replace selective pieces.

If the location...

- starts with `/`, it is treated as an absolute path
- starts with `//` it is treated as a new url on the same scheme
- starts with a scheme (e.g. `http://`), it is treated as a fully-qualified URL
- starts with ./ it is treated as a relative path
- starts with # it is treated as a fragment additon or replacement

If the location is an array, it will replace parts of the URL identified by the keys
with the value of those keys.

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td rowspan="3">
				$location
			</td>
			<td>
									<a href="http://php.net/language.types.string">string</a>
				
			</td>
			<td rowspan="3">
				The location to modify with
			</td>
		</tr>
			
		<tr>
			<td>
									<a href="http://php.net/language.types.array">array</a>
				
			</td>
		</tr>
						
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			URL
		</dt>
		<dd>
			The modified URL
		</dd>
	
</dl>


###### Examples


```php
<?php

$url = new Url('http://www.example.com');
$url = $url->modify(['scheme' => 'https', 'host' => 'secure.example.com']);

echo $url->get();

//
// OUTPUT:
//
// https://secure.example.com
//

```
			
```php
<?php

$url = new Url('http://www.example.com/path');
$url = $url->modify('/newpath');

echo $url->get();

//
// OUTPUT:
//
// http://www.example.com/newpath
//

```
			
```php
<?php

$url = new Url('http://www.example.com/deep/path/test');
$url = $url->modify('../newpath?param=value');

echo $url->get();

//
// OUTPUT:
//
// http://www.example.com/deep/newpath?param=value
//

```
			


<hr />

#### <span style="color:#3e6a6e;">removeFromQuery()</span>

Removes one or more parameters from the query string

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td>
				$parameter
			</td>
			<td>
									<a href="http://php.net/language.types.string">string</a>
				
			</td>
			<td>
				A parameter to remove from the query string
			</td>
		</tr>
					
		<tr>
			<td>
				...
			</td>
			<td>
									<a href="http://php.net/language.types.string">string</a>
				
			</td>
			<td>
				
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			URL
		</dt>
		<dd>
			A new URL with the parameter(s) removed from the query
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">replaceInQuery()</span>

Replaces a value in the query string

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td rowspan="3">
				$parameter
			</td>
			<td>
									<a href="http://php.net/language.types.string">string</a>
				
			</td>
			<td rowspan="3">
				The query string parameter
			</td>
		</tr>
			
		<tr>
			<td>
									<a href="http://php.net/language.types.array">array</a>
				
			</td>
		</tr>
								
		<tr>
			<td rowspan="3">
				$value
			</td>
			<td>
									<a href="http://php.net/language.types.string">string</a>
				
			</td>
			<td rowspan="3">
				The value to set the parameter to
			</td>
		</tr>
			
		<tr>
			<td>
									<a href="http://php.net/language.types.array">array</a>
				
			</td>
		</tr>
						
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			URL
		</dt>
		<dd>
			A new URL with the parameter(s) replaced
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">normalizePath()</span>

Normalizes the URL

##### Details

This will consolidate multiple slashes in a row, reduce back path segments (`..`) and
remove current path segments (`.`).

###### Returns

<dl>
	
		<dt>
			void
		</dt>
		<dd>
			Provides no return value.
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">normalizePort()</span>

Normalizes the URL port depending on scheme

##### Details

This will remove the port if the port matches the default for the scheme.

###### Returns

<dl>
	
		<dt>
			void
		</dt>
		<dd>
			Provides no return value.
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">normalizeQuery()</span>

Normalizes the Query

##### Details

Parse a query set as a string or set empty arrays if invalid value is provided.

###### Returns

<dl>
	
		<dt>
			void
		</dt>
		<dd>
			Provides no return value.
		</dd>
	
</dl>






