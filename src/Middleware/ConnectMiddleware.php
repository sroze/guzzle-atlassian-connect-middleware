<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect package.
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

            $url = str_replace($this->appContext, "", $request->getUri());

            $this->auth
                ->getTokenInstance()
                ->setQueryString($request->getMethod(), $url);

            foreach ($this->auth->getHeaders() as $key => $value) {
                $request = $request->withHeader($key, $value);
            }

            foreach ($this->auth->getQueryParameters() as $key => $value) {
                $request = $request->withUri(Uri::withQueryValue($request->getUri(), $key, $value));
            }

            return $handler($request, $options);
        };
    }
}
