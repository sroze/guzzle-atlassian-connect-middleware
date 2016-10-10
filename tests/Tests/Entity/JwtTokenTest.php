<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Tests\Entity;

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
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInF' .
                'zaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIxNzQ4YzMifQ.eneZ' .
                'Ncg42dCVpB4krbqktMByMPXv1QYbiV-M50Q212A',
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
                'qsh'     => '909144220b8eb4799623bdbb198a01485d4a7ad975d261cdfe14dafe021748c3',
                'sub'     => $tokenValues['subject'],
                'context' => $tokenValues['context']
            ],
            $token->buildPayload()
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInF' .
                'zaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIxNzQ4YzMiLCJjb25' .
                '0ZXh0Ijp7InVzZXIiOnsidXNlcktleSI6IkJhdG1hbiIsInVzZXJuYW1lIjoiYnJ1Y2V3YXluZSIsImRpc3BsYXlOYW1lIjoiQnJ' .
                '1Y2llIn19LCJzdWIiOiJ0aGVTdWJqZWN0IiwiYXVkIjoiQXVkaWVuY2UifQ.VfvMt5u4yFNdF2j_MK1EB1A663v9NHuDxylnb' .
                'ee0k1c',
            $token->sign()
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInF' .
            'zaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIxNzQ4YzMiLCJjb25' .
            '0ZXh0Ijp7InVzZXIiOnsidXNlcktleSI6IkJhdG1hbiIsInVzZXJuYW1lIjoiYnJ1Y2V3YXluZSIsImRpc3BsYXlOYW1lIjoiQnJ' .
            '1Y2llIn19LCJzdWIiOiJ0aGVTdWJqZWN0IiwiYXVkIjoiQXVkaWVuY2UifQ.VfvMt5u4yFNdF2j_MK1EB1A663v9NHuDxylnb' .
            'ee0k1c',
            (string) $token
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
                'qsh' => '909144220b8eb4799623bdbb198a01485d4a7ad975d261cdfe14dafe021748c3',
            ],
            $token->buildPayload()
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInF' .
                'zaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIxNzQ4YzMifQ.eneZ' .
                'Ncg42dCVpB4krbqktMByMPXv1QYbiV-M50Q212A',
            $token->sign()
        );

        $this->assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0SXNzdWVyIiwiaWF0IjoxMjM0NTY3LCJleHAiOjEyMzgxNjcsInF' .
            'zaCI6IjkwOTE0NDIyMGI4ZWI0Nzk5NjIzYmRiYjE5OGEwMTQ4NWQ0YTdhZDk3NWQyNjFjZGZlMTRkYWZlMDIxNzQ4YzMifQ.eneZ' .
            'Ncg42dCVpB4krbqktMByMPXv1QYbiV-M50Q212A',
            (string) $token
        );


    }
}
