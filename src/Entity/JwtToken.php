<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Entity;

use Adlogix\GuzzleAtlassianConnect\Helpers\Qsh;
use Firebase\JWT\JWT;

/**
 * Class JwtToken
 *
 * The JWT standard can be found there
 * @see     https://jwt.io/
 *
 * JWT made by atlassian sits there
 * @see     https://developer.atlassian.com/static/connect/docs/latest/concepts/understanding-jwt.html
 *
 * You can test your JWT token validity against Atlassian there
 * @see     http://jwt-decoder.herokuapp.com/jwt/decode
 *
 * @package Adlogix\GuzzleAtlassianConnect\Entity
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class JwtToken
{
    /**
     * @var string
     */
    private $audience;

    /**
     * @var object
     */
    private $context;

    /**
     * @var int
     */
    private $validityDuration;

    /**
     * @var int
     */
    private $issuedAtTime;

    /**
     * @var string
     */
    private $issuer;


    /**
     * @var string
     */
    private $queryStringHash;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $subject;


    /**
     * JwtToken constructor.
     *
     * @param string $issuer
     * @param string $secret
     * @param null   $issuedAtTime
     * @param int    $validityDuration = 3600
     */
    public function __construct($issuer, $secret, $issuedAtTime = null, $validityDuration = 3600)
    {
        $this->issuer = $issuer;
        $this->secret = $secret;
        $this->issuedAtTime = ($issuedAtTime) ?: time();
        $this->validityDuration = $validityDuration;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->sign();
    }

    /**
     * @param bool $encode
     *
     * @return string
     */
    public function sign($encode = true)
    {

        if (null == $this->queryStringHash) {
            throw new \LogicException('You should provide a Query String before calling sign');
        }

        $payload = [
            'iss' => $this->issuer,
            'iat' => $this->issuedAtTime,
            'exp' => $this->issuedAtTime + $this->validityDuration,
            'qsh' => $this->queryStringHash
        ];

        if (null !== $this->context) {
            $payload['context'] = $this->context;
        }

        if (null !== $this->subject) {
            $payload['sub'] = $this->subject;
        }

        if (null !== $this->audience) {
            $payload['aud'] = $this->audience;
        }

        if (!$encode) {
            return $payload;
        }
        return JWT::encode($payload, $this->secret);
    }

    /**
     * @param int $validityDuration
     *
     * @return JwtToken
     */
    public function setValidityDuration($validityDuration)
    {
        $this->validityDuration = $validityDuration;
        return $this;
    }

    /**
     * @param int $issuedAtTime
     *
     * @return JwtToken
     */
    public function setIssuedAtTime($issuedAtTime)
    {
        $this->issuedAtTime = $issuedAtTime;
        return $this;
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @return $this
     */
    public function setQueryString($method, $url)
    {
        $this->queryStringHash = Qsh::create($method, $url);
        return $this;
    }

    /**
     * @param string $audience
     *
     * @return JwtToken
     */
    public function setAudience($audience)
    {
        $this->audience = $audience;
        return $this;
    }


    /**
     * @param object $context
     *
     * @return JwtToken
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @param string $subject
     *
     * @return JwtToken
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
}
