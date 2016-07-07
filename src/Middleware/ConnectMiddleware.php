<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Middleware;

use Adlogix\GuzzleAtlassianConnect\Security\AuthenticationInterface;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * Class ConnectMiddleware
 * @package Adlogix\GuzzleAtlassianConnect\Middleware
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class ConnectMiddleware
{
    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * @var string
     */
    private $appContext;

    /**
     * ConnectMiddleware constructor.
     *
     * @param AuthenticationInterface $auth
     * @param string                  $appContext
     */
    public function __construct(AuthenticationInterface $auth, $appContext)
    {
        $this->auth = $auth;
        $this->appContext = $appContext;
    }

    /**
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use (&$handler) {

            $url = rawurldecode(str_replace($this->appContext, "", $request->getUri()));

            $this->auth->setQueryString($request->getMethod(), $url);

            $request = $this->appendHeaders($request);
            $request = $this->appendQueryParams($request);


            return $handler($request, $options);
        };
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    private function appendHeaders(RequestInterface $request)
    {
        $headers = $this->auth->getHeaders();
        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }
        return $request;
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    private function appendQueryParams(RequestInterface $request)
    {
        $queryParams = $this->auth->getQueryParameters();
        foreach ($queryParams as $key => $value) {
            $uri = Uri::withQueryValue($request->getUri(), $key, $value);
            $request = $request->withUri($uri);
        }
        return $request;
    }
}
