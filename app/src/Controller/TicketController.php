<?php

// src/Controller/TicketController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Ticket;
use App\Form\TicketType;

#[IsGranted('ROLE_USER')]
#[Route('/tickets')]
class TicketController extends AbstractController
{
    
    #[Route('/', name: "tickets_list")]
    public function list(EntityManagerInterface $em): Response
    {
        $tickets = $em->getRepository(Ticket::class)->findAll();
        // dd($tickets);

        return $this->render('tickets/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }
    
    #[Route('/new', name: "tickets_new")]
    public function new (Request $request, EntityManagerInterface $em): Response
    {
        $ticket = new Ticket();

        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $ticket = $form->getData();
            $ticket->setUser($this->getUser());

            $em->persist($ticket);
            $em->flush();

            $this->addFlash(
                'success',
                'Ticket bien créé !'
            );
            
            return $this->redirectToRoute('tickets_list');
        }
    
        return $this->renderForm('tickets/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{ticketId}', name: "tickets_show")]
    public function show(?int $ticketId, EntityManagerInterface $em): Response
    {
        $ticket = $em->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException(
                'No ticket found for id ' . $ticketId
            );
        }

        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{ticketId}/edit', name: "tickets_edit")]
    #[Entity('ticket', options: ['id' => 'ticketId'])]
    public function edit (Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            /*if ($ticket->getUser() == $this->getUser()) {*/
                $ticket = $form->getData();
                $em->persist($ticket);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Ticket bien mise à jour !'
                );
            /*} else {
                $this->addFlash(
                    'danger',
                    'Vous n\'avez pas les droits pour cette action !'
                );
            }*/
            
            return $this->redirectToRoute('tickets_list');
        }
    
        return $this->renderForm('tickets/edit.html.twig', [
            'form' => $form,
            'ticket' => $ticket
        ]);
    }
}