<?php
/**
 * This file is part of the Adlogix package.
 *
 * (c) Allan Segebarth <allan@adlogix.eu>
 * (c) Jean-Jacques Courtens <jjc@adlogix.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Helpers;

/**
 * Class QSH
 * QSH is a Query String Hash requested by Atlassian in the JWT token
 * @see     https://developer.atlassian.com/static/connect/docs/latest/concepts/understanding-jwt.html#qsh
 * @package Adlogix\GuzzleAtlassianConnect\Helpers
 * @author  Cedric Michaux <cedric@adlogix.eu>
 */
class Qsh
{
    /**
     * @param $method
     * @param $url
     *
     * @return string
     */
    public static function create($method, $url)
    {
        $method = strtoupper($method);

        $parts = parse_url($url);
        $path = $parts['path'];

        $canonicalQuery = '';
        if (array_key_exists('query', $parts)) {
            $canonicalQuery = self::getCanonicalQuery($parts['query']);
        }

        $qshString = $method . '&' . $path . '&' . $canonicalQuery;
        $qsh = hash('sha256', $qshString);

        return $qsh;
    }

    /**
     * @param string $query
     *
     * @return string
     */
    private static function getCanonicalQuery($query = '')
    {
        if (!empty($query)) {
            $queryParts = explode('&', $query);
            $query = '';
            $queryArray = [];

            foreach ($queryParts as $queryPart) {
                $pieces = explode('=', $queryPart, 2);

                $key = rawurlencode($pieces[0]);
                $value = rawurlencode($pieces[1]);

                $queryArray[$key][] = $value;
            }

            ksort($queryArray);

            foreach ($queryArray as $key => $pieceOfQuery) {
                $pieceOfQuery = implode(',', $pieceOfQuery);
                $query .= $key . '=' . $pieceOfQuery . '&';
            }

            $query = rtrim($query, '&');
        }

        return $query;
    }
}
