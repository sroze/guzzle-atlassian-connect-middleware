<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Security;

use Adlogix\GuzzleAtlassianConnect\Entity\JwtToken;

/**
 * Class AbstractAuthentication
 * @package Adlogix\GuzzleAtlassianConnect\Security
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
abstract class AbstractAuthentication implements AuthenticationInterface
{
    /**
     * @var JwtToken
     */
    protected $token;

    /**
     * AbstractAuthentication constructor.
     *
     * @param string $key
     * @param string $sharedSecret
     */
    public function __construct($key, $sharedSecret)
    {
        $this->token = new JwtToken($key, $sharedSecret);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParameters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenInstance()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryString($method, $url)
    {
        return $this->token->setQueryString($method, $url);
    }
}
