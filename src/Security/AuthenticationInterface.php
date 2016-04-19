<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Security;

/**
 * Interface AuthenticationInterface
 * @package Adlogix\GuzzleAtlassianConnect\Security
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
interface AuthenticationInterface
{

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @return array
     */
    public function getQueryParameters();

    /**
     * @return JwtToken
     */
    public function &getTokenInstance();
}
