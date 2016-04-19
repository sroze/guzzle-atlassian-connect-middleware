<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Tests\Tests\Security;

use Adlogix\GuzzleAtlassianConnect\Security\HeaderAuthentication;
use Adlogix\GuzzleAtlassianConnect\Tests\TestCase;

/**
 * Class HeaderAuthenticationTest
 * @package Adlogix\GuzzleAtlassianConnect\Tests\Tests\Security
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class HeaderAuthenticationTest extends TestCase
{
    /**
     * @test
     * @expectedException \LogicException
     */
    public function getHeaders_WithoutQSH_ThrowsException()
    {
        $headerAuthentication = new HeaderAuthentication('key', 'sharedSecret');
        $headerAuthentication->getHeaders();
    }

    /**
     * @test
     */
    public function getHeaders_WithQSH_Success()
    {
        $headerAuthentication = new HeaderAuthentication('key', 'sharedSecret');
        $token = $headerAuthentication->getTokenInstance();

        $token->setIssuedAtTime(123456);
        $token->setValidityDuration(123457);
        $token->setQueryString('GET', '/some/path');

        $this->assertEquals(
            ['Authorization' => 'JWT eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJrZXkiLCJpYXQiOjEyMzQ1NiwiZXhwIjoyNDY5MTMsInFzaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIxNzQ4YzMifQ.Uy8F4KCV4MQ1U6biaWt8EHufYmmEuKKbYX406SoAgCA'],
            $headerAuthentication->getHeaders()
        );

        $this->assertEmpty(
            $headerAuthentication->getQueryParameters()
        );

    }
}
