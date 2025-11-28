<?php

namespace OHMedia\AlertBundle\Service;

use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\AlertBundle\Security\Voter\AlertVoter;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;

class AlertNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(AlertVoter::INDEX, new Alert())) {
            return (new NavLink('Alerts', 'alert_index'))
                ->setIcon('exclamation-triangle');
        }

        return null;
    }
}
