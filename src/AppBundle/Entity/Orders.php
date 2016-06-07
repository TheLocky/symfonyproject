<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdersRepository")
 * @GRID\Source(columns="id, user, itemsJson, datetime, success")
 */
class Orders
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
     * @ORM\Column(name="user", type="string", length=64)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="itemsJson", type="string", length=9999)
     */
    private $itemsJson;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="string", length=30)
     */
    private $datetime;

    /**
     * @var bool
     *
     * @ORM\Column(name="success", type="boolean")
     */
    private $success;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return Orders
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set itemsJson
     *
     * @param string $itemsJson
     *
     * @return Orders
     */
    public function setItemsJson($itemsJson)
    {
        $this->itemsJson = $itemsJson;

        return $this;
    }

    /**
     * Get itemsJson
     *
     * @return string
     */
    public function getItemsJson()
    {
        return $this->itemsJson;
    }

    /**
     * Set datetime
     *
     * @param string $datetime
     *
     * @return Orders
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set success
     *
     * @param boolean $success
     *
     * @return Orders
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }
}

