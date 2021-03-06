<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'vendor/autoload.php';

use Adlogix\GuzzleAtlassianConnect\Middleware\ConnectMiddleware;
use Adlogix\GuzzleAtlassianConnect\Security\HeaderAuthentication;
use Adlogix\GuzzleAtlassianConnect\Tests\Helpers\Payload;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * See the 'installed' webhook on how to recover this payload.
 *
 * The sharedSecret is given by the application we installed the add-on to,
 * this is needed to sign our request and to validate the requests from the application.
 */
$payload = new Payload('payload.json');
$applicationKey = 'eu.adlogix.atlassian-connect';


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
    new HeaderAuthentication($applicationKey, $payload->getSharedSecret()),
    $payload->getBaseUrl()
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
        'base_uri' => $payload->getBaseUrl(),
        'handler'  => $stack,
        'debug'    => false
    ]
);


/**
 * Since [name-your-app] needs to reach our application to post some information, like the sharedSecret, we have to
 * define some routes.
 * At time of writing Confluence refuses to contact us if the route contains .php so we need to prettify our URLS.
 * Our sample is not the best way to do it, but it's just for the demo.
 */


$app = new Application();
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
]);


/**
 * Our sample descriptor is available at http://atlassian-connect.dev/descriptor.json
 *
 * This is the bare minimal descriptor to be defined.
 *
 * You can validate your descriptor
 * @see https://atlassian-connect-validator.herokuapp.com/validate
 */
$app->get('/descriptor.json', function (Request $request) use ($applicationKey) {

    /*
     * We have to construct the correct URL in order to confluence be able to contact us
     * And the scheme MUST be https in order to confluence accept it.
     */
    $host = $request->getHttpHost();
    $scheme = $request->getScheme();

    if (preg_match('/\.ngrok\.io/', $host)) {
        $scheme = 'https';
    }

    $descriptor = new \Adlogix\GuzzleAtlassianConnect\Entity\Descriptor($scheme . '://' . $host, $applicationKey);

    $descriptor->addScope(\Adlogix\GuzzleAtlassianConnect\Entity\Descriptor::SCOPE_READ)
        ->setLifecycleWebhooks(
            '/installed',
            '/enabled'
        );

    return $descriptor->getJson();
});

/**
 * When we install our add-on into any atlassian app, they will contact us at the URL we define in the 'installed'
 * lifecycle.
 * They will give us a payload containing the sharedSecret we'll need to use to sign our request.
 * For the demo we just save the content to a file.
 */
$app->post('/installed', function (Request $request) {

    $payload = $request->getContent();
    file_put_contents('payload.json', $payload);

    /**
     * Be sure to send a 200 OK response, or the app will tell you that your plugin can't be installed.
     */
    return new Response('OK', 200);
});


/**
 * Even if the documentation tell's you the only needed webhook is the installed one,
 * they won't let you enable the add-on unless you define the route to you 'enabled' webhook.
 */
$app->post('/enabled', function () {
    /**
     * Be sure to send a 200 OK response, or the app will tell you that your plugin can't be enabled.
     */
    return new Response('OK', 200);
});

//Catch all route to run our test code
$app->get('/', function (Application $app) {
    return $app['twig']->render("index.html.twig");
});


$app->get('{url}', function ($url) use ($client) {
    $response = $client->get($url);
    return new Response(
        $response->getBody()->getContents(),
        $response->getStatusCode(),
        $response->getHeaders()
    );
})->assert('url', '.+');


$app->run();
