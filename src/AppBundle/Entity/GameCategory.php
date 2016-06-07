<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 * @GRID\Source(columns="id, name, path")
 */
class GameCategory
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\Column(type="string", length=100)
     */
	protected $name;

	/**
     * @ORM\Column(type="string", length=100)
     */
	protected $path;

	/**
     * @ORM\Column(type="boolean")
     */
	protected $clickable;

	/**
     * @ORM\ManyToMany(targetEntity="SteamGame", mappedBy="categories")
     */
    protected $games;

    public function __construct()
    {
    	$games = new ArrayCollection();
    }

    /**
     * @param SteamGame $game
     */
    public function addGame(SteamGame $game)
    {
        if ($this->games->contains($game)) {
            return;
        }
        $this->games->add($game);
        $game->addSteamGameGroup($this);
    }

    /**
     * @param SteamGame $game
     */
    public function removeGame(SteamGame $game)
    {
        if (!$this->games->contains($game)) {
            return;
        }
        $this->games->removeElement($game);
        $game->removeSteamGameGroup($this);
    }	

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
     * @return GameCategory
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
     * Set path
     *
     * @param string $path
     *
     * @return GameCategory
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set clickable
     *
     * @param boolean $clickable
     *
     * @return GameCategory
     */
    public function setClickable($clickable)
    {
        $this->clickable = $clickable;

        return $this;
    }

    /**
     * Get clickable
     *
     * @return boolean
     */
    public function getClickable()
    {
        return $this->clickable;
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGames()
    {
        return $this->games;
    }
}
