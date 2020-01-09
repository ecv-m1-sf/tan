<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StopRepository")
 */
class Stop implements ImportDatasInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=32)
     */
    private $id;

    /**
     * @var StopTime[]
     * @ORM\OneToMany(targetEntity="StopTime", mappedBy="stop")
     */
    private $stopTimes;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=7)
     */
    private $latitude;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=7)
     */
    private $longitude;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $locationType;

    /**
     * @ORM\ManyToOne(targetEntity="Stop")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isWheelchairBoarding;

    /**
     * Stop constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $description
     * @param float  $latitude
     * @param float  $longitude
     * @param int    $locationType
     * @param Stop   $parent
     * @param bool   $wheelchairBoarding
     */
    public function __construct(string $id, string $name, string $description, float $latitude, float $longitude, int $locationType, ?self $parent, bool $wheelchairBoarding)
    {
        $this->id = $id;
        $this->stopTimes = new ArrayCollection();
        $this->name = $name;
        $this->description = $description;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->locationType = $locationType;
        $this->parent = $parent;
        $this->isWheelchairBoarding = $wheelchairBoarding;
    }

    /**
     * @param array $datas
     *
     * @return Stop
     */
    public static function createFromCsv(array $datas)
    {
        return new self($datas['stop_id'], $datas['stop_name'], $datas['stop_desc'], $datas['stop_lat'], $datas['stop_lon'], $datas['location_type'], $datas['parent'], $datas['wheelchair_boarding']);
    }
}
