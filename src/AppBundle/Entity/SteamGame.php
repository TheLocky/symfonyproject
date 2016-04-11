<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="steamgames")
 */
class SteamGame
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
     * @ORM\Column(type="string", length=100)
     */
	private $name;

	/**
     * @ORM\Column(type="string", length=256)
     */
	private $header_image;

	/**
     * @ORM\Column(type="string", length=100)
     */
	private $release_date;

	/**
     * @ORM\Column(type="string", length=100)
     */
	private $developers;

	/**
     * @ORM\Column(type="string", length=256)
     */
	private $website;

	/**
     * @ORM\Column(type="string", length=40)
     */
	private $price;

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
     * Set name
     *
     * @param string $name
     *
     * @return SteamGame
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set headerImage
     *
     * @param string $headerImage
     *
     * @return SteamGame
     */
    public function setHeaderImage($headerImage)
    {
        $this->header_image = $headerImage;

        return $this;
    }

    /**
     * Get headerImage
     *
     * @return string
     */
    public function getHeaderImage()
    {
        return $this->header_image;
    }

    /**
     * Set releaseDate
     *
     * @param string $releaseDate
     *
     * @return SteamGame
     */
    public function setReleaseDate($releaseDate)
    {
        $this->release_date = $releaseDate;

        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return string
     */
    public function getReleaseDate()
    {
        return $this->release_date;
    }

    /**
     * Set developers
     *
     * @param string $developers
     *
     * @return SteamGame
     */
    public function setDevelopers($developers)
    {
        $this->developers = $developers;

        return $this;
    }

    /**
     * Get developers
     *
     * @return string
     */
    public function getDevelopers()
    {
        return $this->developers;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return SteamGame
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return SteamGame
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }
}
