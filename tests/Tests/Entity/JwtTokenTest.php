<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\Confluence\Client\Tests\Entity;

use Adlogix\GuzzleAtlassianConnect\Entity\JwtToken;
use Adlogix\GuzzleAtlassianConnect\Tests\TestCase;

/**
 * Class JwtTokenTest
 * @package Adlogix\Confluence\Client\Tests\Entity
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class JwtTokenTest extends TestCase
{

    /**
     * @test
     * @expectedException \LogicException
     */
    public function sign_WithoutQsh_ThrowsException()
    {
        $token = new JwtToken('testIssuer', 'secretKey');
        $token->sign();
    }


    /**
     * @test
     */
    public function sign_WithQsh_Success()
    {
        $token = new JwtToken('testIssuer', 'secretKey', 1234567, 3600);
        $token->setQueryString('GET', '/some/path');


        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInFzaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIxNzQ4YzMifQ.eneZNcg42dCVpB4krbqktMByMPXv1QYbiV-M50Q212A',
            $token->sign()
        );
    }

    /**
     * @test
     */
    public function sign_allProperties_Success()
    {

        $tokenValues = [
            'issuer'       => 'testIssuer',
            'key'          => 'secretKey',
            'issuedAtTime' => 1234567,
            'validity'     => 3600,
            'audience'     => 'Audience',
            'context'      => [
                'user' => [
                    'userKey'     => 'Batman',
                    'username'    => 'brucewayne',
                    'displayName' => 'Brucie'
                ]
            ],
            'subject'      => 'theSubject'
        ];

        $token = new JwtToken(
            $tokenValues['issuer'],
            $tokenValues['key'],
            $tokenValues['issuedAtTime'],
            $tokenValues['validity']
        );


        $token
            ->setAudience($tokenValues['audience'])
            ->setContext($tokenValues['context'])
            ->setQueryString('get', 'some/path')
            ->setSubject($tokenValues['subject']);


        $this->assertEquals(
            [
                'aud'     => $tokenValues['audience'],
                'iss'     => $tokenValues['issuer'],
                'iat'     => $tokenValues['issuedAtTime'],
                'exp'     => $tokenValues['issuedAtTime'] + $tokenValues['validity'],
                'qsh'     => '9e877a61dc37c91bcca16763456003cdd9007cd3ff39afadc6eca8656215ad13',
                'sub'     => $tokenValues['subject'],
                'context' => $tokenValues['context']
            ],
            $token->sign(false)
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInFzaCI6IjllODc3YTYxZGMzN2M5MWJjY2ExNjc2MzQ1NjAwM2NkZDkwMDdjZDNmZjM5YWZhZGM2ZWNhODY1NjIxNWFkMTMiLCJjb250ZXh0Ijp7InVzZXIiOnsidXNlcktleSI6IkJhdG1hbiIsInVzZXJuYW1lIjoiYnJ1Y2V3YXluZSIsImRpc3BsYXlOYW1lIjoiQnJ1Y2llIn19LCJzdWIiOiJ0aGVTdWJqZWN0IiwiYXVkIjoiQXVkaWVuY2UifQ.JWA7Ry27ZGt5mACuI0eOXy_Cx-ccmNXQa1ckRNZiT8Y',
            $token->sign()
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInFzaCI6IjllODc3YTYxZGMzN2M5MWJjY2ExNjc2MzQ1NjAwM2NkZDkwMDdjZDNmZjM5YWZhZGM2ZWNhODY1NjIxNWFkMTMiLCJjb250ZXh0Ijp7InVzZXIiOnsidXNlcktleSI6IkJhdG1hbiIsInVzZXJuYW1lIjoiYnJ1Y2V3YXluZSIsImRpc3BsYXlOYW1lIjoiQnJ1Y2llIn19LCJzdWIiOiJ0aGVTdWJqZWN0IiwiYXVkIjoiQXVkaWVuY2UifQ.JWA7Ry27ZGt5mACuI0eOXy_Cx-ccmNXQa1ckRNZiT8Y',
            $token
        );

    }

    /**
     * @test
     */
    public function sign_someProperties_Success()
    {

        $tokenValues = [
            'issuer'       => 'testIssuer',
            'key'          => 'secretKey',
            'issuedAtTime' => 1234567,
            'validity'     => 3600,
        ];

        $token = new JwtToken(
            $tokenValues['issuer'],
            $tokenValues['key'],
            $tokenValues['issuedAtTime'],
            $tokenValues['validity']
        );


        $token
            ->setQueryString('get', 'some/path');


        $this->assertEquals(
            [
                'iss' => $tokenValues['issuer'],
                'iat' => $tokenValues['issuedAtTime'],
                'exp' => $tokenValues['issuedAtTime'] + $tokenValues['validity'],
                'qsh' => '9e877a61dc37c91bcca16763456003cdd9007cd3ff39afadc6eca8656215ad13',
            ],
            $token->sign(false)
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInFzaCI6IjllODc3YTYxZGMzN2M5MWJjY2ExNjc2MzQ1NjAwM2NkZDkwMDdjZDNmZjM5YWZhZGM2ZWNhODY1NjIxNWFkMTMifQ.rF3mreI96dnq67QbBvZJNA3B9WSpYx-Xi_nuWPQikWE',
            $token->sign()
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInFzaCI6IjllODc3YTYxZGMzN2M5MWJjY2ExNjc2MzQ1NjAwM2NkZDkwMDdjZDNmZjM5YWZhZGM2ZWNhODY1NjIxNWFkMTMifQ.rF3mreI96dnq67QbBvZJNA3B9WSpYx-Xi_nuWPQikWE',
            $token
        );


    }
}
