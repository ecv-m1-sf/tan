<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StopTimeRepository")
 */
class StopTime implements ImportDatasInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;
    /**
     * @var Trip
     * @ORM\ManyToOne(targetEntity="Trip", inversedBy="stopTimes")
     */
    private $trip;

    /**
     * @var Stop
     * @ORM\ManyToOne(targetEntity="Stop", inversedBy="stopTimes")
     */
    private $stop;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    private $arrival;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    private $departure;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $stopSequence;

    /**
     * StopTime constructor.
     *
     * @param Trip      $trip
     * @param Stop      $stop
     * @param \DateTime $arrival
     * @param \DateTime $departure
     * @param int       $stopSequence
     */
    public function __construct(Trip $trip, Stop $stop, \DateTime $arrival, \DateTime $departure, int $stopSequence)
    {
        $this->trip = $trip;
        $this->stop = $stop;
        $this->arrival = $arrival;
        $this->departure = $departure;
        $this->stopSequence = $stopSequence;
    }

    /**
     * @param array $datas
     *
     * @return StopTime
     */
    public static function createFromCsv(array $datas): self
    {
        return new self(
            $datas['trip'], $datas['stop'],
            \DateTime::createFromFormat('H:i:s', $datas['arrival_time']),
            \DateTime::createFromFormat('H:i:s', $datas['departure_time']),
            $datas['stop_sequence']
        );
    }
}
