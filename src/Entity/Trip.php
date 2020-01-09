<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TripRepository")
 */
class Trip implements ImportDatasInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=64)
     */
    private $id;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="Route", inversedBy="trips")
     */
    private $route;

    /**
     * @var StopTime[]
     * @ORM\OneToMany(targetEntity="StopTime", mappedBy="trip")
     */
    private $stopTimes;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    private $headsign;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $direction;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isWheelchairAccessible;

    /**
     * Trip constructor.
     *
     * @param string $id
     * @param Route  $route
     * @param string $headsign
     * @param string $direction
     * @param bool   $isWheelchairAccessible
     */
    private function __construct(string $id, Route $route, string $headsign, string $direction, bool $isWheelchairAccessible)
    {
        $this->id = $id;
        $this->route = $route;
        $this->stopTimes = new ArrayCollection();
        $this->headsign = $headsign;
        $this->direction = $direction;
        $this->isWheelchairAccessible = $isWheelchairAccessible;
    }

    /**
     * @param array $datas
     *
     * @return Trip
     */
    public static function createFromCsv(array $datas): self
    {
        return new self($datas['trip_id'], $datas['route'], $datas['trip_headsign'], $datas['direction_id'], 1 === $datas['wheelchair_accessible']);
    }
}
