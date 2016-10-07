<?php
/*
 * This file is part of the adlogix/guzzle-atlassian-connect package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'vendor/autoload.php';

use Adlogix\GuzzleAtlassianConnect\Middleware\ConnectMiddleware;
use Adlogix\GuzzleAtlassianConnect\Security\QueryParamAuthentication;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

/**
 * See the 'installed' webhook on how to recover this payload.
 *
 * The sharedSecret is given by the application we installed the add-on to,
 * this is needed to sign our request and to validate the requests from the application.
 */
$sharedSecret = '';
$baseUrl = '';
if (file_exists('payload.json')) {
    $payload = json_decode(file_get_contents('payload.json'));
    $sharedSecret = $payload->sharedSecret;
    $baseUrl = $payload->baseUrl;
}

/**
 * Here we create the middleware;
 * for the authentication method we give the key we defined in our descriptor,
 * and the second parameter is the sharedSecret given by atlassian when we installed the add-on.
 *
 * For more info on the descriptor,
 * @see https://developer.atlassian.com/static/connect/docs/latest/modules/
 *
 * For more info on how to get the sharedKey, you need to define the installed lifecycle in your descriptor.
 * @see https://developer.atlassian.com/static/connect/docs/latest/modules/lifecycle.html
 *
 * The second parameter ro create the middleware is the full path to the application we want to connect to.
 * For the demo we use Confluence which resides at http://atlassian-confluence.dev/confluence
 *
 * If your sharedSecret is empty, there's no need to try to contact the application,
 * so be sure you received the 'enabled' webhook call before trying to contact it.
 */
$middleware = new ConnectMiddleware(
    new QueryParamAuthentication('eu.adlogix.atlassian-connect', $sharedSecret),
    $baseUrl
);


/**
 * We start to build ou Guzzle Client by defining the HandlerStack and pushing our middleware in it.
 */
$stack = HandlerStack::create();
$stack->push($middleware);

/**
 * And the Client creation
 */
$client = new Client(
    [
        'base_uri' => $baseUrl.'/',
        'handler'  => $stack,
        'debug'    => true
    ]
);


$response = $client->get('rest/api/space');
var_dump($response->getBody()->getContents());

echo "\r\n\r\n=======================================================================================================" .
        "==============================================================================================\r\n\r\n";

$response = $client->get(
    'download/attachments/197288/Seller%20Admin%20logo%20stats.png?version=1&modificationDate=1459423644007&api=v2'
);
var_dump($response->getBody()->getContents());
