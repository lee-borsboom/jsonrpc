*This project has been orphaned. Expect to have to maintain it yourself if you choose to use it. I'll accept pull requests that look reasonable but beyond that no further work will be added to this project.*



JSON-RPC
=======

JSON-RPC 2.0 package for Laravel 4, including full support for notifications and batches.


Installation
------------
Note that while this package is not stable, you need to ensure that your composer.json file has the minimum-stability flag set to "dev" otherwise installation of this package will fail.


###### Assuming you have Composer installed globally, run the following commands in the root of your Laravel project:

	composer config repositories.leeb/jsonrpc vcs https://github.com/lee-borsboom/jsonrpc.git

	composer require leeb/jsonrpc:master
	
	php artisan config:publish leeb/jsonrpc


Configuration
-------------

After publishing the configuration file (see step 3 in the installation process), the JSON-RPC configuration file will be located in &lt;app path&gt;/config/packages/leeb/jsonrpc/config.php

#### Laravel 4 ServiceProvider

And add the following to your app/config/app.php file :

In the Service Providers array : 'Leeb\Jsonrpc\JsonrpcServiceProvider',

#### Routing

By default, this package will only route requests that match http://www.yourhost.com/jsonrpc. This can be configured by setting the **route_prefix** configuration option. Note that to match all routes, set this to null or remove the **route_prefix** configuration setting from the configuration file entirely.

#### Resolution

This package expects that requests will be made that have a method property in the format of _Class.method_. It is expected that any such class should be accessible via IoC.

The **resolution_pattern** configuration option defines how the method from the request will be resolved to a concrete class.

The package is configured initially to look for the class _\Class\ClassController_. For example, the package will look for _\Records\RecordsController_ and call the _listRecords_ method, given the following request:

	{
		"id" : 123,
		"jsonrpc" : "2.0",
		"method" : "Records.listRecords"
	}

The initial configuration setting is:

	\{class}\{class}Controller

Note how the {class} is replaced with the class from the method in the JSON-RPC call.

#### Custom resolver

Another, more flexible method of resolution is to provide a **callable** in the **resolver** configurable option. If provided, this should be a **callable** that accepts one parameter (the method string direct from the client request) and return a string representing the class to use in action the request (including the full namespace).

#### Exception handling

By default any exceptions in the controllers should be caught by the JSON-RPC service, which will generate a JSON-RPC internal error. An exception handler can be registered with the JSON-RPC service that can manipulate exceptions into alternative JSON-RPC errors.

The exception handler is added as a closure in the JSON-RPC config, for example:

    return array(
        'exception_handler' => function ($request, $exception) {
            Log::error($exception);
            return new JsonrpcError($request->getId(), -32000, 'Error message', 'additional data');
        }
    );

Usage
-----

After installing and configuring this package you're ready to rock 'n' roll. Simply create a controller (or whatever you'd prefer to call it) with the desired methods.

The body of the JSON-RPC params structure is accessible using Laravel's _Input::all()_, _Input::get_ and _Request::input_ methods.

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
		public function listRecords()
		{
			return \Response::make(array(
				"artist" => \Input::get('artist'),
				"albums" => array(
					'...Like Clockwork',
					'Era Vulgaris',
					'Lullabies to Paralyze',
					'Songs for the Deaf',
					'Rated R',
					'Queens of the Stone Age'
				)
			));
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

Events
------
This package adds the following events:
	
#### jsonrpc.beforeExecution

	Event::listen('jsonrpc.beforeExecution', function ($handler_object, $handler_method_name) {
		$params = \Input::all();
		$params['automatically_injected_value'] = 5;
		\Input::replace($params);
	});


#### jsonrpc.beforeOutput

	\Event::listen('jsonrpc.beforeOutput', function ($response, $handler_object, $handler_method_name) {
		if (isset($response->error)) {
			// Do something in the event of an error
		} else {
			$response->result['automatically_injected_value'] = 5;
		}
	});
