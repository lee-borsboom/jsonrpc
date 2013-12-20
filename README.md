JSON-RPC
=======

JSON-RPC 2.0 package for Laravel 4


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

This package expects that requests will be made that have a method property in the format of _Class.method_. The **resolution_pattern** configuration option defines how that will be resolved to a class. The package is configured initially to look for the class _\Class\ClassController_. For example, the package will look for _\Records\RecordsController_ and call the _list_ method, given the following request:

	{
		"id" : 123,
		"jsonrpc" : "2.0",
		"method" : "Records.list"
	}
