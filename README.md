JSON-RPC
=======

JSON-RPC 2.0 package for Laravel 4, including full support for notifications and batches.


Installation
------------

###### Assuming you have Composer installed globally, run the following commands in the root of your Laravel project:

	composer config repositories.leeb/jsonrpc vcs https://github.com/lee-borsboom/jsonrpc.git

	composer require leeb/jsonrpc:master
	
	php artisan config:publish leeb/jsonrpc


Configuration
-------------

After publishing the configuration file (see step 3 in the installation process), the JSON-RPC configuration file will be located in &lt;app path&gt;/config/packages/leeb/jsonrpc/config.php

#### Routing

By default, this package will only route requests that match http://www.yourhost.com/jsonrpc. This can be configured by setting the **route_prefix** configuration option. Note that to match all routes, you should remove this configuration option completely.

#### Resolution

This package expects that requests will be made that have a method property in the format of _Class.method_. It is expected that any such class should be accessible via IoC.

The **resolution_pattern** configuration option defines how the method from the request will be resolved to a concrete class.

The package is configured initially to look for the class _\Class\ClassController_. For example, the package will look for _\Records\RecordsController_ and call the _list_ method, given the following request:

	{
		"id" : 123,
		"jsonrpc" : "2.0",
		"method" : "Records.list"
	}

The initial configuration setting is:

	\{class}\{class}Controller

Note how the {class} is replaced with the class from the method in the JSON-RPC call.

Usage
-----

After installing and configuring this package you're ready to rock 'n' roll. Simply create a controller (or whatever you'd prefer to call it) with the desired methods.

Your controller method will be passed a _Request_ object, which has two method of particular significance. The _data_ method accepts a property name and returns the corresponding value from the _params_ object, or _null_ if no matching property exists.

The _rawData_ provides direct access to the raw params object.

###### Sample request

	{
		"id" : 123,
		"jsonrpc" : "2.0",
		"method" : "Records.list",
		"params" : {
			"artist" : "Queens of the Stone Age"
		}
	}

###### Sample controller


	namespace Records;
	
	class RecordsController
	{
		public function list($request)
		{
			return array(
				"artist" => $request->data('artist'),
				"albums" => array(
					'...Like Clockwork',
					'Era Vulgaris',
					'Lullabies to Paralyze',
					'Songs for the Deaf',
					'Rated R',
					'Queens of the Stone Age'
				)
			);
		}
	}

###### Sample response

	{
		"id": 123,
		"jsonrpc": "2.0",
		"result": {
			"artist": "Queens of the Stone Age",
			"albums": [
				"...Like Clockwork",
				"Era Vulgaris",
				"Lullabies to Paralyze",
				"Songs for the Deaf",
				"Rated R",
				"Queens of the Stone Age"
			]
		}
	}
