<?php

namespace Mgi\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resource
 *
 * @ORM\Table(name="resource")
 * @ORM\Entity(repositoryClass="Mgi\CoreBundle\Repository\ResourceRepository")
 */
class Resource
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="resource_name", type="string", length=50, unique=true)
     */
    private $resourceName;

    /**
     * @var string
     *
     * @ORM\Column(name="resource_version", type="string", length=10)
     */
    private $resourceVersion;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set resourceName
     *
     * @param string $resourceName
     * @return $this
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    /**
     * Get resourceName
     *
     * @return string 
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Set resourceVersion
     *
     * @param string $resourceVersion
     * @return $this
     */
    public function setResourceVersion($resourceVersion)
    {
        $this->resourceVersion = $resourceVersion;

        return $this;
    }

    /**
     * Get resourceVersion
     *
     * @return string 
     */
    public function getResourceVersion()
    {
        return $this->resourceVersion;
    }
}
