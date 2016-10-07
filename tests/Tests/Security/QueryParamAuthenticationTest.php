<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Tests\Tests\Security;

use Adlogix\GuzzleAtlassianConnect\Security\QueryParamAuthentication;
use Adlogix\GuzzleAtlassianConnect\Tests\TestCase;

/**
 * Class QueryParamAuthenticationTest
 * @package Adlogix\GuzzleAtlassianConnect\Tests\Tests\Security
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class QueryParamAuthenticationTest extends TestCase
{

    /**
     * @test
     * @expectedException \LogicException
     */
    public function getQueryParams_WithoutQSH_ThrowsException()
    {
        $queryParamAuth = new QueryParamAuthentication('key', 'sharedSecret');
        $queryParamAuth->getQueryParameters();
    }


    /**
     * @test
     */
    public function getQueryParams_WithQSH_Success()
    {
        $queryParamAuth = new QueryParamAuthentication('key', 'sharedSecret');
        $token = $queryParamAuth->getTokenInstance();

        $token->setIssuedAtTime(123456);
        $token->setValidityDuration(123457);
        $token->setQueryString('GET', '/some/path');

        $this->assertEquals(
            [
                'jwt' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJrZXkiLCJpYXQiOjEyMzQ1NiwiZXhwIjoyNDY5MTMsI' .
                            'nFzaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIx' .
                            'NzQ4YzMifQ.Uy8F4KCV4MQ1U6biaWt8EHufYmmEuKKbYX406SoAgCA'
            ],
            $queryParamAuth->getQueryParameters()
        );

        $this->assertEmpty(
            $queryParamAuth->getHeaders()
        );

    }
}
