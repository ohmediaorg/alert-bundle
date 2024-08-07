<?php

namespace OHMedia\AlertBundle\Controller;

use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\AlertBundle\Form\AlertType;
use OHMedia\AlertBundle\Repository\AlertRepository;
use OHMedia\AlertBundle\Security\Voter\AlertVoter;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BootstrapBundle\Service\Paginator;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class AlertController extends AbstractController
{
    public function __construct(private AlertRepository $alertRepository)
    {
    }

    #[Route('/alerts', name: 'alert_index', methods: ['GET'])]
    public function index(Paginator $paginator): Response
    {
        $newAlert = new Alert();

        $this->denyAccessUnlessGranted(
            AlertVoter::INDEX,
            $newAlert,
            'You cannot access the list of alerts.'
        );

        $qb = $this->alertRepository->createQueryBuilder('a');
        $qb->orderBy('a.id', 'desc');

        return $this->render('@backend/alert/alert_index.html.twig', [
            'pagination' => $paginator->paginate($qb, 20),
            'new_alert' => $newAlert,
            'attributes' => $this->getAttributes(),
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

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->alertRepository->save($alert, true);

                $this->addFlash('notice', 'The alert was created successfully.');

                return $this->redirectToRoute('alert_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/alert/alert_create.html.twig', [
            'form' => $form->createView(),
            'alert' => $alert,
        ]);
    }

    #[Route('/alert/{id}/edit', name: 'alert_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Alert $alert,
    ): Response {
        $this->denyAccessUnlessGranted(
            AlertVoter::EDIT,
            $alert,
            'You cannot edit this alert.'
        );

        $form = $this->createForm(AlertType::class, $alert);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->alertRepository->save($alert, true);

                $this->addFlash('notice', 'The alert was updated successfully.');

                return $this->redirectToRoute('alert_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@backend/alert/alert_edit.html.twig', [
            'form' => $form->createView(),
            'alert' => $alert,
        ]);
    }

    #[Route('/alert/{id}/delete', name: 'alert_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Alert $alert,
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

        return $this->render('@backend/alert/alert_delete.html.twig', [
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
