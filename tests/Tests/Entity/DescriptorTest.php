<?php
/**
 * This file is part of the adlogix/guzzle-atlassian-connect-middleware package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Adlogix\GuzzleAtlassianConnect\Tests\Tests\Entity;

use Adlogix\GuzzleAtlassianConnect\Entity\Descriptor;
use Adlogix\GuzzleAtlassianConnect\Tests\TestCase;

class DescriptorTest extends TestCase
{

    /**
     * @test
     */
    public function construct_WithValidParams_shouldSetBaseUrlAndAuthorizationAndKey()
    {
        $descriptor = new Descriptor("http://atlassian-connect.dev", "eu.adlogix.atlassian-connect");
        $descriptorArray = $descriptor->getArray();

        $this->assertArrayHasKey("baseUrl", $descriptorArray);
        $this->assertArrayHasKey("key", $descriptorArray);
        $this->assertArrayHasKey("authentication", $descriptorArray);

        $this->assertEquals('jwt', $descriptorArray['authentication']['type']);
    }

    /**
     * @test
     */
    public function addScope_WithValidParams_shouldAddScopeItem()
    {
        $descriptor = new Descriptor("http://atlassian-connect.dev", "eu.adlogix.atlassian-connect");

        $descriptor->addScope(Descriptor::SCOPE_WRITE)
            ->addScope(Descriptor::SCOPE_CONFLUENCE_SPACE_ADMIN);

        $descriptorArray = $descriptor->getArray();


        $this->assertEquals(['write', 'space_admin'], $descriptorArray['scopes']);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function addScope_WithInvalidParam_shouldThrowException()
    {
        $descriptor = new Descriptor("http://atlassian-connect.dev", "eu.adlogix.atlassian-connect");

        $descriptor->addScope("foo");

    }


    /**
     * @test
     */
    public function removeScope_WithValidParams_shouldRemoveScopeItem()
    {
        $descriptor = new Descriptor("http://atlassian-connect.dev", "eu.adlogix.atlassian-connect");

        $descriptor->addScope(Descriptor::SCOPE_WRITE)
            ->addScope(Descriptor::SCOPE_CONFLUENCE_SPACE_ADMIN)
            ->removeScope(Descriptor::SCOPE_CONFLUENCE_SPACE_ADMIN);

        $descriptorArray = $descriptor->getArray();



        $this->assertEquals(['write'], $descriptorArray['scopes']);
    }


    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function removeScope_WithInvalidParam_shouldThrowException()
    {
        $descriptor = new Descriptor("http://atlassian-connect.dev", "eu.adlogix.atlassian-connect");

        $descriptor->removeScope("foo");

    }
}
