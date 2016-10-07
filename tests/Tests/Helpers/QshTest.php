<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Tests\Helpers;

use Adlogix\GuzzleAtlassianConnect\Helpers\Qsh;
use Adlogix\GuzzleAtlassianConnect\Tests\TestCase;

/**
 * Class QshTest
 * @package Adlogix\Confluence\Client\Helpers
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class QshTest extends TestCase
{
    /**
     * @test
     */
    public function create_WithoutQueryParams_Success()
    {
        // QSH can be generated at: http://jwt-decoder.herokuapp.com/jwt/decode
        $this->assertEquals(
            '909144220b8eb4799623bdbb198a01485d4a7ad975d261cdfe14dafe021748c3',
            Qsh::create('GET', '/some/path')
        );
    }

    /**
     * @test
     */
    public function create_WithQueryParams_Success()
    {
        $this->assertEquals(
            '536378242f0cd9a2a0b909a30a8ab1fb608f27891ec4acf250f42e66c04ca220',
            Qsh::create('GET', '/some/path?with=parameter&other=parameter')
        );
    }

    /**
     * @test
     * @dataProvider queryStringHash_dataprovider
     *
     * @param $hash
     * @param $method
     * @param $uri
     */
    public function create_WithDifferentMethods_Success($hash, $method, $uri)
    {
        $this->assertEquals(
            $hash,
            Qsh::create($method, $uri)
        );
    }

    /**
     * @return array
     */
    public function queryStringHash_dataprovider()
    {
        return [
            [
                '909144220b8eb4799623bdbb198a01485d4a7ad975d261cdfe14dafe021748c3',
                'GET',
                '/some/path'
            ],
            [
                'cc65372f0e8ccc4b75ddc210f3f7b959192104e52fd3e28a35300a56e0bb65a7',
                'POST',
                '/some/path'
            ],
            [
                '3c2f888516e992119d2d9c0da7d297cd636356c7ccbc5ea0a5e75b4fc6287491',
                'PUT',
                '/some/path'
            ],
            [
                '5b331d63ee824dc8d118d8f8dd9e432bbbbc95420167712fd3d1791a18e3a4eb',
                'DELETE',
                '/some/path'
            ],

            [
                '536378242f0cd9a2a0b909a30a8ab1fb608f27891ec4acf250f42e66c04ca220',
                'GET',
                '/some/path?with=parameter&other=parameter'
            ],
            [
                '01d94fc8d70ea25060095393fa271e52a32d40cc0cfbe7b6816e4b2263486f27',
                'GET',
                '/some/path?parameter=with,a,comma'
            ],
            [
                'bb418494de898bc5a57abce9f792c92174e63c98af5218465cd426c1055b18cc',
                'GET',
                '/some/path?parameter=with.a.dot'
            ],
            [
                'df3b8c04449f386f811843da7796da2ce2e405ee87d64b4088039d3c116e4752',
                'GET',
                '/some/path?parameter=with=an=equals'
            ],
            [
                '61f32b9c7e25fa2c1e1ede4ce6ae251551dfff097a6811f9758fc1fc4a80ca6e',
                'GET',
                '/some/path?parameter=with(a)parenthese)'
            ],
            [
                '3582dfdb579693bc82e985822d1b7cbfc62c503fe84ed87a41438cc29f7c24c6',
                'GET',
                '/some/path?parameter=with a space'
            ],
            [
                '9850b42d39e071f911840092e0a41ad41c0997f7b8e0efb04fccb6eff0bd1ac1',
                'GET',
                '/some/path?parameter=with!a!bang'
            ],
            [
                '661d573ce6e75704d85d415ad5f8e535c40876f9f6f36796f56ffb84cc9ff181',
                'GET',
                '/some/path?parameter=with~a~tilde'
            ],
            [
                'f82c96bccf303b4ffa58e77bb9e9055f5ccd244204cd6d0990822bde7802f961',
                'GET',
                '/some/path?parameter=mention = jsmith and creator != jsmith'
            ],
            [
                'c178717f4c46c0caa0ef6a00df4d13b54d00971e10420b6dd9f0487214adc04e',
                'GET',
                '/some/path?parameter=title !<>~ "win*"'
            ],
            [
                '2a0fa49d89b83efd5f5b154b9b8c50bfd0df8c4305d7d38b58093cb59143c20d',
                'GET',
                '/download/attachments/197288/Seller%20Admin%20logo%20stats.png?version=1&modificationDate=1459423644007&api=v2'
            ],
            [
                'c8483d04d4d62762cf010a238bc611e55970256bf528162d23a4999abc3553b2',
                'GET',
                'rest/api/space'
            ]
        ];
    }

}
