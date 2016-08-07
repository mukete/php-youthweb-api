<?php

namespace Youthweb\Api\Tests;

use Youthweb\Api\Client;
use InvalidArgumentException;

class ClientTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function testSetUrlReturnsClient()
	{
		$client = new Client();

		$this->assertInstanceOf('Youthweb\Api\Client', $client->setUrl('http://test.local'));
	}

	/**
	 * @test
	 */
	public function testGetUrlReturnsValueFromSetUrl()
	{
		$client = (new Client())->setUrl('http://test.local');

		$this->assertSame('http://test.local', $client->getUrl());
	}

	/**
	 * @test
	 */
	public function testSetUserCredentialsReturnsClient()
	{
		$client = new Client();

		$this->assertInstanceOf('Youthweb\Api\Client', $client->setUserCredentials('Username', 'User-Token'));
	}

	/**
	 * @test
	 */
	public function testGetUserCredentialReturnsValueFromSetUserCredentials()
	{
		$client = (new Client())->setUserCredentials('Username', 'User-Token');

		$this->assertSame('Username', $client->getUserCredential('username'));
		$this->assertSame('User-Token', $client->getUserCredential('token_secret'));
	}

	/**
	 * @test
	 */
	public function testGetUserCredentialWithWrongKeyThrowsException()
	{
		$client = (new Client())->setUserCredentials('Username', 'User-Token');

		$this->setExpectedException(
			'UnexpectedValueException',
			'"foobar" is not a valid key for user credentials.'
		);

		$foo = $client->getUserCredential('foobar');
	}

	/**
	 * @test
	 */
	public function testGetHttpClientReturnsHttpClient()
	{
		$method = new \ReflectionMethod(
			'Youthweb\Api\Client', 'getHttpClient'
		);

		$method->setAccessible(true);

		$this->assertInstanceOf('Youthweb\Api\HttpClientInterface', $method->invoke(new Client));
	}

	/**
	 * @test
	 */
	public function testSetHttpClientReturnsClient()
	{
		$client = new Client();

		$stub = $this->getMock('Youthweb\Api\HttpClientInterface');

		$this->assertInstanceOf('Youthweb\Api\Client', $client->setHttpClient($stub));
	}

	/**
	 * @test
	 */
	public function testGetCacheProviderReturnsPsrCacheItemPool()
	{
		$client = new Client();

		$this->assertInstanceOf('Psr\Cache\CacheItemPoolInterface', $client->getCacheProvider());
	}

	/**
	 * @test
	 */
	public function testSetCacheProviderReturnsClient()
	{
		$client = new Client();

		$stub = $this->getMock('Psr\Cache\CacheItemPoolInterface');

		$this->assertInstanceOf('Youthweb\Api\Client', $client->setCacheProvider($stub));
	}

	/**
	 * @test
	 */
	public function testBuildCacheKeyReturnsString()
	{
		$client = new Client();

		$this->assertSame('php_youthweb_api.foobar', $client->buildCacheKey('foobar'));
	}

	/**
	 * @test
	 * @dataProvider getResoursesClassesProvider
	 */
	public function testGetApiInstance($resource_name, $class_name)
	{
		$client = new Client();

		$this->assertInstanceOf($class_name, $client->getResource($resource_name));
	}

	/**
	 * Resources DataProvider
	 */
	public function getResoursesClassesProvider()
	{
		return array(
			array('auth', 'Youthweb\Api\Resource\Auth'),
			array('stats', 'Youthweb\Api\Resource\Stats'),
			array('users', 'Youthweb\Api\Resource\Users'),
		);
	}

	/**
	 * @test
	 */
	public function testGetUnknownResourceThrowsException()
	{
		$client = new Client();

		$this->setExpectedException(
			'InvalidArgumentException',
			'The resource "foobar" does not exists.'
		);

		$client->getResource('foobar');
	}

	/**
	 * @test
	 */
	public function testParseResponseReturnsObject()
	{
		$body = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
			->disableOriginalConstructor()
			->getMock();

		$body->expects($this->any())
			->method('getContents')
			->willReturn('{"meta":{"this":"that"}}');

		$response = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
			->disableOriginalConstructor()
			->getMock();

		$response->expects($this->any())
			->method('getBody')
			->willReturn($body);

		$http_client = $this->getMockBuilder('Youthweb\Api\HttpClientInterface')
			->disableOriginalConstructor()
			->getMock();

		$http_client->expects($this->any())
			->method('send')
			->willReturn($response);

		$client = new Client();
		$client->setHttpClient($http_client);

		$this->assertInstanceOf('\Art4\JsonApiClient\Document', $client->get('foobar'));
	}

	/**
	 * @test
	 */
	public function testHandleClientExceptionWithResponseException()
	{
		$body = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
			->getMock();

		$body->expects($this->any())
			->method('getContents')
			->willReturn('{"errors":[{"status":"401","title":"Unauthorized"}]}');

		$response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
			->getMock();

		$response->expects($this->any())
			->method('getBody')
			->willReturn($body);

		$exception = $this->getMockBuilder('GuzzleHttp\Exception\ClientException')
			->disableOriginalConstructor()
			->getMock();

		$exception->expects($this->any())
			->method('getResponse')
			->willReturn($response);

		$http_client = $this->getMockBuilder('Youthweb\Api\HttpClientInterface')
			->getMock();

		$http_client->expects($this->any())
			->method('send')
			->will($this->throwException($exception));

		$client = new Client();
		$client->setHttpClient($http_client);

		$this->setExpectedException(
			'Exception',
			'Unauthorized'
		);

		$client->get('foobar');
	}

	/**
	 * @test
	 */
	public function testHandleClientExceptionWithDetailResponseException()
	{
		$body = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
			->getMock();

		$body->expects($this->any())
			->method('getContents')
			->willReturn('{"errors":[{"status":"401","title":"Unauthorized","detail":"Detailed error message"}]}');

		$response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
			->getMock();

		$response->expects($this->any())
			->method('getBody')
			->willReturn($body);

		$exception = $this->getMockBuilder('GuzzleHttp\Exception\ClientException')
			->disableOriginalConstructor()
			->getMock();

		$exception->expects($this->any())
			->method('getResponse')
			->willReturn($response);

		$http_client = $this->getMockBuilder('Youthweb\Api\HttpClientInterface')
			->getMock();

		$http_client->expects($this->any())
			->method('send')
			->will($this->throwException($exception));

		$client = new Client();
		$client->setHttpClient($http_client);

		$this->setExpectedException(
			'Exception',
			'Detailed error message'
		);

		$client->get('foobar');
	}

	/**
	 * @test
	 */
	public function testHandleClientExceptionWithException()
	{
		$body = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
			->getMock();

		$body->expects($this->any())
			->method('getContents')
			->willReturn('{"meta":{"error":"foobar"}}');

		$response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
			->getMock();

		$response->expects($this->any())
			->method('getBody')
			->willReturn($body);

		$exception = $this->getMockBuilder('\Exception')
			->disableOriginalConstructor()
			->getMock();

		$exception->expects($this->any())
			->method('getResponse')
			->willReturn($response);

		$http_client = $this->getMockBuilder('Youthweb\Api\HttpClientInterface')
			->getMock();

		$http_client->expects($this->any())
			->method('send')
			->will($this->throwException($exception));

		$client = new Client();
		$client->setHttpClient($http_client);

		$this->setExpectedException(
			'Exception',
			'The server responses with an unknown error.'
		);

		$client->get('foobar');
	}
}
