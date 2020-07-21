<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass=SeasonRepository::class)
 */
class Season extends AbstractEntity {
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alternativeTitle;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var Podcast
     * @ORM\ManyToOne(targetEntity="Podcast", inversedBy="seasons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $podcast;

    /**
     * @var Publisher
     * @ORM\ManyToOne(targetEntity="Publisher", inversedBy="seasons")
     * @ORM\JoinColumn(nullable=true)
     */
    private $publisher;

    /**
     * @var Collection|Contribution[]
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="season")
     */
    private $contributions;

    /**
     * @var Collection|Episode[]
     * @ORM\OneToMany(targetEntity="Episode", mappedBy="season")
     */
    private $episodes;

    public function __construct() {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string {
        // TODO: Implement __toString() method.
    }
}
