<?php namespace Leeb\Jsonrpc;

use Illuminate\Support\ServiceProvider;

class JsonrpcServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('leeb/jsonrpc');
		
		\App::bind('Leeb\Jsonrpc\Interfaces\RequestInterface', function ($app, $data) {
			return new \Leeb\Jsonrpc\Request($data[0]);
		});

		\App::bind('Leeb\Jsonrpc\JsonrpcResponse', function ($app, $data) {
			return new \Leeb\Jsonrpc\JsonrpcResponse($data[0], $data[1], $data[2]);
		});

		\App::singleton('Leeb\Jsonrpc\Interfaces\JsonrpcResponseBuilderInterface',
			'Leeb\Jsonrpc\JsonrpcResponseBuilder');

		\App::bind('Leeb\Jsonrpc\RoutableRequest', function ($app, $data) {
			$resolver = \App::make('Leeb\Jsonrpc\Interfaces\MethodResolverInterface');
			$response_builder = \App::make('Leeb\Jsonrpc\Interfaces\JsonrpcResponseBuilderInterface');
			return new \Leeb\Jsonrpc\RoutableRequest($resolver, $response_builder, $data[0]);
		});

		\App::bind('Leeb\Jsonrpc\JsonrpcError', function ($app, $data) {
			return new \Leeb\Jsonrpc\JsonrpcError($data[0], $data[1], $data[2]);
		});

		\App::bind('Leeb\Jsonrpc\RoutableNotification', function ($app, $data) {
			$resolver = \App::make('Leeb\Jsonrpc\Interfaces\MethodResolverInterface');
			return new \Leeb\Jsonrpc\RoutableNotification($resolver, $data[0]);
		});

		\App::bind('Leeb\Jsonrpc\RoutableBatch', function ($app, $data) {
			return new \Leeb\Jsonrpc\RoutableBatch($data[0]);
		});

		\App::singleton('Leeb\Jsonrpc\Interfaces\RouterInterface', 'Leeb\Jsonrpc\Router');

		\App::singleton('Leeb\Jsonrpc\Interfaces\MethodResolverInterface',
			'Leeb\Jsonrpc\MethodResolver');

		\App::singleton('Leeb\Jsonrpc\Interfaces\JsonrpcConfigurationInterface',
			'Leeb\Jsonrpc\JsonrpcConfiguration');

		\App::singleton('Leeb\Jsonrpc\Interfaces\RequestValidatorInterface',
			'Leeb\Jsonrpc\RequestValidator');

		\App::singleton('Leeb\Jsonrpc\Interfaces\RawRequestInterpreterInterface',
			'Leeb\Jsonrpc\RawRequestInterpreter');
	}

	public function boot()
	{
		$configuration = \App::make('Leeb\Jsonrpc\Interfaces\JsonrpcConfigurationInterface');
		$route_prefix = $configuration->getRoutePrefix();

		if (empty($route_prefix)) {
			$this->routeAllToJsonrpc();
		} else {
			$this->routePrefixToJsonrpc($route_prefix);
		}
	}

	public function routePrefixToJsonrpc($route_prefix)
	{
		\App::before(function () use ($route_prefix)
		{
			\Route::post($route_prefix, function ()
			{
				\App::make('Leeb\Jsonrpc\Interfaces\RouterInterface')->route();
			});
		});
	}

	public function routeAllToJsonrpc()
	{
		\App::before(function ()
		{
			\Route::post('{all}', function ($path)
			{
				\App::make('Leeb\Jsonrpc\Interfaces\RouterInterface')->route();

			})->where('all', '.*');
		});
	}
}