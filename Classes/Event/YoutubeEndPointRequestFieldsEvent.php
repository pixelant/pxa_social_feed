<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Event;

final class YoutubeEndPointRequestFieldsEvent
{
    private $fields;

    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields($fields): void
    {
        $this->fields = $fields;
    }
}
