<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Tests\Middleware;

use Adlogix\GuzzleAtlassianConnect\Entity\JwtToken;
use Adlogix\GuzzleAtlassianConnect\Middleware\ConnectMiddleware;
use Adlogix\GuzzleAtlassianConnect\Security\AuthenticationInterface;
use Adlogix\GuzzleAtlassianConnect\Tests\TestCase;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ConnectMiddlewareTest
 * @package Adlogix\GuzzleAtlassianConnect\Tests\Middleware
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class ConnectMiddlewareTest extends TestCase
{

    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var string
     */
    private $appContext;

    /**
     * Sets up the tests
     */
    public function setUp()
    {

        $this->uri = new Uri('http:://appurl.com/application/some/path?expand=body.view,children.page.body.view&title=Seller+-+User+Tutorials');
        $this->appContext = 'http:://appurl.com/application';

        $this->auth = $this->createMock(AuthenticationInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        

        $this->request->expects($this->once())
            ->method('getMethod')
            ->willReturn('get');



        $this->auth->expects($this->once())
            ->method('setQueryString')
            ->with('get', '/some/path?expand=body.view,children.page.body.view&title=Seller+-+User+Tutorials')
            ->willReturnSelf();
    }

    /**
     * @test
     */
    public function invoke_WithHeaders_Success()
    {

        $this->auth->expects($this->once())
            ->method('getHeaders')
            ->willReturn([
                'Authorization' => "XXXX",
                'OtherOption' => 'XXXX'
            ]);

        $this->auth->expects($this->once())
            ->method('getQueryParameters')
            ->willReturn([]);

        $this->request->expects($this->once())
            ->method('getUri')
            ->willReturn($this->uri);

        $this->request->expects($this->exactly(2))
            ->method('withHeader')
            ->withConsecutive(
                ['Authorization', "XXXX"],
                ['OtherOption', 'XXXX']
            )
            ->willReturnSelf();


        $middleware = new ConnectMiddleware($this->auth, $this->appContext);

        $callable = $middleware(
            function (RequestInterface $actualRequest, array $options) {
                $this->assertEquals($this->request, $actualRequest);
                $this->assertEquals(['Hello World'], $options);
            }
        );

        $callable($this->request, ['Hello World']);
    }

    /**
     * @test
     */
    public function invoke_QueryParameters_Success()
    {
        $this->auth
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn([]);

        $this->auth
            ->expects($this->once())
            ->method('getQueryParameters')
            ->willReturn([
                'jwt' => 'YYYY',
                'otherParam' => 'YYYY'
            ]);


        $this->request->expects($this->exactly(3))
            ->method('getUri')
            ->willReturn($this->uri);

        $this->request->expects($this->exactly(2))
            ->method('withUri')
            ->withConsecutive(
                Uri::withQueryValue($this->uri, 'jwt', 'YYYY'),
                Uri::withQueryValue($this->uri, 'otherParam', 'YYYY')
            )
            ->willReturnSelf();

        $middleware = new ConnectMiddleware($this->auth, $this->appContext);

        $callable = $middleware(
            function (RequestInterface $actualRequest, array $options) {
                $this->assertEquals($this->request, $actualRequest);
                $this->assertEquals(['Hello World'], $options);
            }
        );

        $callable($this->request, ['Hello World']);
    }

}
