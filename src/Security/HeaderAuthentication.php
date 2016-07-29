<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Security;

/**
 * Class HeaderAuthentication
 * @package Adlogix\GuzzleAtlassianConnect\Security
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class HeaderAuthentication extends AbstractAuthentication
{

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return [
            "Authorization" => sprintf('JWT %s', $this->token->sign())
        ];

    }
}
