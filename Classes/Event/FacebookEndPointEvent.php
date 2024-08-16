<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Event;

final class FacebookEndPointEvent
{
    private $endPoint;

    public function __construct($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    /**
     * @return mixed
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * @param mixed $endPoint
     * @return self
     */
    public function setEndPoint($endPoint): self
    {
        $this->endPoint = $endPoint;
        return $this;
    }
}
