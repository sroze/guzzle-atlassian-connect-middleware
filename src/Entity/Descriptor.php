<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Adlogix\GuzzleAtlassianConnect\Entity;

class Descriptor
{
    const SCOPE_NONE = "none";
    const SCOPE_READ = "read";
    const SCOPE_WRITE = "write";
    const SCOPE_DELETE = "delete";
    const SCOPE_ADMIN = "admin";
    const SCOPE_ACT_AS_USER = "act_a_user";

    const SCOPE_JIRA_PROJECT_ADMIN = "project_admin";
    const SCOPE_CONFLUENCE_SPACE_ADMIN = "space_admin";

    /**
     * @var array
     */
    private $descriptor = [
        'authentication' => [
            'type' => 'jwt'
        ],
        'scopes'         => []
    ];


    /**
     * Descriptor constructor.
     *
     * @param string $baseUrl
     * @param string $key
     */
    public function __construct($baseUrl, $key)
    {
        $this->descriptor['baseUrl'] = $baseUrl;
        $this->descriptor['key'] = $key;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->descriptor["name"] = $name;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->descriptor['description'] = $description;
        return $this;
    }

    /**
     * @param string $scope
     *
     * @return $this
     * @throws \Exception
     */
    public function addScope($scope)
    {
        $this->validateScopeValue($scope);

        if (false !== $this->getScopeKey($scope)) {
            return $this;
        }

        $this->descriptor['scopes'][] = $scope;
        return $this;
    }

    /**
     * @param string $scope
     *
     * @return bool
     */
    private function validateScopeValue($scope)
    {
        switch ($scope) {
            case self::SCOPE_ACT_AS_USER:
            case self::SCOPE_ADMIN:
            case self::SCOPE_NONE:
            case self::SCOPE_READ:
            case self::SCOPE_WRITE:
            case self::SCOPE_DELETE:
            case self::SCOPE_JIRA_PROJECT_ADMIN:
            case self::SCOPE_CONFLUENCE_SPACE_ADMIN:
                return true;
        }
        throw new \InvalidArgumentException(sprintf("Unknown scope %s", $scope));
    }

    /**
     * @param $scope
     *
     * @return mixed
     */
    private function getScopeKey($scope)
    {
        $key = array_search($scope, $this->descriptor['scopes']);
        return $key;
    }

    /**
     * @param string $scope
     *
     * @return $this
     * @throws \Exception
     */
    public function removeScope($scope)
    {
        $this->validateScopeValue($scope);

        $key = $this->getScopeKey($scope);
        if (false === $key) {
            return $this;
        }

        unset($this->descriptor['scopes'][$key]);
        return $this;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->descriptor;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->descriptor);
    }

    /**
     * @return $this
     */
    public function enableLicensing()
    {
        $this->descriptor['enableLicensing'] = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableLicensing()
    {
        $this->descriptor['enableLicensing'] = false;
        return $this;
    }

    /**
     * @param int $version
     *
     * @return $this
     */
    public function setApiVersion($version)
    {
        $this->descriptor['apiVersion'] = $version;
        return $this;
    }

    /**
     * @param string $name
     * @param string $url
     *
     * @return $this
     */
    public function addLink($name, $url)
    {
        $this->descriptor['links'][$name] = $url;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeLink($name)
    {
        unset($this->descriptor['links'][$name]);
        return $this;
    }

    /**
     * @param string $name
     * @param string $url
     *
     * @return $this
     */
    public function setVendor($name, $url = '')
    {
        $this->descriptor['vendor'] = [
            'name' => $name,
            'url'  => $url
        ];
        return $this;
    }

    /**
     * @param string $installed
     * @param string $enabled
     * @param string $disabled
     * @param string $uninstalled
     *
     * @return $this
     */
    public function setLifecycleWebhooks($installed, $enabled, $disabled = null, $uninstalled = null)
    {
        $this->descriptor['lifecycle'] = [
            'installed' => $installed,
            'enabled'   => $enabled,
        ];

        if (null !== $disabled) {
            $this->descriptor['lifecycle']['disabled'] = $disabled;
        }

        if (null !== $uninstalled) {
            $this->descriptor['lifecycle']['uninstalled'] = $uninstalled;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array  $description
     *
     * @return $this
     */
    public function addModule($name, array $description)
    {
        if (!isset($this->descriptor['modules'])) {
            $this->descriptor['modules'] = [];
        }
        $this->descriptor['modules'][$name] = $description;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeModule($name)
    {
        unset($this->descriptor['modules'][$name]);
        return $this;
    }
}
