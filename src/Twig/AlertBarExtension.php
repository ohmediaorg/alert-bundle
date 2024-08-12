<?php

namespace OHMedia\AlertBundle\Twig;

use OHMedia\AlertBundle\Repository\AlertRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AlertBarExtension extends AbstractExtension
{
    public function __construct(private AlertRepository $alertRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('alert_bar', [$this, 'alertBar'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function alertBar(Environment $twig): string
    {
        $alert = $this->alertRepository->getActive();

        if (!$alert) {
            return '';
        }

        if ($alert->isDismissible() && isset($_COOKIE[$alert->getCookieName()])) {
            return '';
        }

        return $twig->render('@OHMediaAlert/alert_bar.html.twig', [
            'alert' => $alert,
        ]);
    }
}
