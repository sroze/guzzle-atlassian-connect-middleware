<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Security;

/**
 * Class QueryParamAuthentication
 *
 * This Authentication method is
 *
 * @package Adlogix\GuzzleAtlassianConnect\Security
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class QueryParamAuthentication extends AbstractAuthentication
{

    /**
     * {@inheritdoc}
     */
    public function getQueryParameters()
    {
        return [
            "jwt" => $this->token->sign()
        ];
    }
}
