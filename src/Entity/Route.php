<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RouteRepository")
 */
class Route implements ImportDatasInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     */
    private $id;
    /**
     * @var Trip[]
     * @ORM\OneToMany(targetEntity="Trip", mappedBy="route")
     */
    private $trips;
    /**
     * @ORM\Column(type="string", length=128)
     */
    private $shortName;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $longName;
    /**
     * @ORM\Column(type="text")
     */
    private $description;
    /**
     * @ORM\Column(type="string", length=6)
     */
    private $color;
    /**
     * Route constructor.
     *
     * @param $id
     * @param Trip[] $trips
     * @param $shortName
     * @param $longName
     * @param $description
     * @param $color
     */
    private function __construct(string $id, string $shortName, string $longName, string $description, string $color)
    {
        $this->id = $id;
        $this->trips = new ArrayCollection();
        $this->shortName = $shortName;
        $this->longName = $longName;
        $this->description = $description;
        $this->color = $color;
    }
    /**
     * @param array $datas
     *
     * @return Route
     */
    public static function createFromCsv(array $datas): self
    {
        return new self($datas['route_id'], $datas['route_short_name'], $datas['route_long_name'], $datas['route_desc'], $datas['route_text_color']);
    }
}
