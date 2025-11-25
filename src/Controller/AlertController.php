<?php

namespace OHMedia\AlertBundle\Controller;

use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\AlertBundle\Form\AlertType;
use OHMedia\AlertBundle\Repository\AlertRepository;
use OHMedia\AlertBundle\Security\Voter\AlertVoter;
use OHMedia\BackendBundle\Form\MultiSaveType;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\TimezoneBundle\Service\Timezone;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class AlertController extends AbstractController
{
    public function __construct(
        private AlertRepository $alertRepository,
        private Timezone $timezone,
    ) {
    }

    #[Route('/alerts', name: 'alert_index', methods: ['GET'])]
    public function index(): Response
    {
        $newAlert = new Alert();

        $this->denyAccessUnlessGranted(
            AlertVoter::INDEX,
            $newAlert,
            'You cannot access the list of alerts.'
        );

        $alerts = $this->alertRepository->createQueryBuilder('a')
            ->orderBy('a.starts_at', 'desc')
            ->getQuery()
            ->getResult();

        return $this->render('@OHMediaAlert/alert_index.html.twig', [
            'alerts' => $alerts,
            'new_alert' => $newAlert,
            'attributes' => $this->getAttributes(),
            'active_alert' => $this->alertRepository->getActive(),
        ]);
    }

    #[Route('/alert/create', name: 'alert_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $alert = new Alert();

        $this->denyAccessUnlessGranted(
            AlertVoter::CREATE,
            $alert,
            'You cannot create a new alert.'
        );

        $form = $this->createForm(AlertType::class, $alert);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->alertRepository->save($alert, true);

                $this->addFlash('notice', 'The alert was created successfully.');

                return $this->redirectForm($alert, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        $this->setActiveAlertFlash($alert);

        return $this->render('@OHMediaAlert/alert_create.html.twig', [
            'form' => $form->createView(),
            'alert' => $alert,
        ]);
    }

    #[Route('/alert/{id}/edit', name: 'alert_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] Alert $alert,
    ): Response {
        $this->denyAccessUnlessGranted(
            AlertVoter::EDIT,
            $alert,
            'You cannot edit this alert.'
        );

        $form = $this->createForm(AlertType::class, $alert);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->alertRepository->save($alert, true);

                $this->addFlash('notice', 'The alert was updated successfully.');

                return $this->redirectForm($alert, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        $this->setActiveAlertFlash($alert);

        return $this->render('@OHMediaAlert/alert_edit.html.twig', [
            'form' => $form->createView(),
            'alert' => $alert,
        ]);
    }

    private function redirectForm(Alert $alert, FormInterface $form): Response
    {
        if ($form->get('save')->get('keep_editing')->isClicked()) {
            return $this->redirectToRoute('alert_edit', [
                'id' => $alert->getId(),
            ]);
        } elseif ($form->get('save')->get('add_another')->isClicked()) {
            return $this->redirectToRoute('alert_create');
        } else {
            return $this->redirectToRoute('alert_index');
        }
    }

    private function setActiveAlertFlash(Alert $alert)
    {
        $activeAlert = $this->alertRepository->getActive();

        $message = null;

        if ($activeAlert === $alert) {
            $this->addFlash('info', 'This is the current active alert.');
        } elseif ($activeAlert) {
            $timezone = new \DateTimeZone($this->timezone->get());
            $startsAt = $activeAlert->getStartsAt()->setTimezone($timezone);

            if ($activeAlert->getEndsAt()) {
                $endsAt = $activeAlert->getEndsAt()->setTimezone($timezone);

                $message = sprintf(
                    'The current active alert started on %s and will expire on %s.',
                    $startsAt->format('M j, Y @ g:ia'),
                    $endsAt->format('M j, Y @ g:ia'),
                );
            } else {
                $message = sprintf(
                    'The current active alert started on %s and is not set to expire.',
                    $startsAt->format('M j, Y @ g:ia'),
                );
            }
        }

        if ($message) {
            $this->addFlash('info', $message);
        }
    }

    #[Route('/alert/{id}/delete', name: 'alert_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] Alert $alert,
    ): Response {
        $this->denyAccessUnlessGranted(
            AlertVoter::DELETE,
            $alert,
            'You cannot delete this alert.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->alertRepository->remove($alert, true);

                $this->addFlash('notice', 'The alert was deleted successfully.');

                return $this->redirectToRoute('alert_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        $activeAlert = $this->alertRepository->getActive();

        if ($alert === $activeAlert) {
            $this->addFlash('warning', 'This is the current active alert.');
        }

        return $this->render('@OHMediaAlert/alert_delete.html.twig', [
            'form' => $form->createView(),
            'alert' => $alert,
        ]);
    }

    private function getAttributes(): array
    {
        return [
            'create' => AlertVoter::CREATE,
            'delete' => AlertVoter::DELETE,
            'edit' => AlertVoter::EDIT,
        ];
    }
}
