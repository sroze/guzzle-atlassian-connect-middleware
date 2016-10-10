<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
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
        $path = self::getCorrectPath($parts['path']);

        $canonicalQuery = '';
        if (array_key_exists('query', $parts)) {
            $canonicalQuery = self::getCanonicalQuery($parts['query']);
        }

        $qshString = $method . '&' . $path . '&' . $canonicalQuery;
        $qsh = hash('sha256', $qshString);

        return $qsh;
    }

    /**
     *
     * @param string $path
     *
     * @return mixed
     */
    private static function getCorrectPath($path)
    {

        $path = str_replace(' ', '%20', $path);

        if ('/' !== $path[0]) {
            $path = '/' . $path;
        }

        return $path;
    }

    /**
     * @param string $query
     *
     * @return string
     */
    private static function getCanonicalQuery($query = '')
    {

        if (empty($query)) {
            return $query;
        }

        $queryArray = self::encodeQueryParts($query);

        ksort($queryArray);

        $query = self::buildQuery($queryArray);

        return $query;
    }

    /**
     * @param string $query
     *
     * @return array
     */
    private static function encodeQueryParts($query)
    {
        $queryParts = explode('&', $query);
        $queryArray = [];

        foreach ($queryParts as $queryPart) {
            $pieces = explode('=', $queryPart, 2);

            $key = rawurlencode($pieces[0]);
            $value = rawurlencode($pieces[1]);

            $queryArray[$key][] = $value;
        }
        return $queryArray;
    }

    /**
     * @param array $queryArray
     *
     * @return string
     */
    private static function buildQuery($queryArray)
    {
        $query = '';
        foreach ($queryArray as $key => $pieceOfQuery) {
            $pieceOfQuery = implode(',', $pieceOfQuery);
            $query .= $key . '=' . $pieceOfQuery . '&';
        }

        $query = rtrim($query, '&');
        return $query;
    }
}
