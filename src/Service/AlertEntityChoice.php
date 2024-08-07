<?php

namespace OHMedia\AlertBundle\Service;

use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class AlertEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Alerts';
    }

    public function getEntities(): array
    {
        return [Alert::class];
    }
}
