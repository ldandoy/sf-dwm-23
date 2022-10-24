<?php

// src/Controller/TicketController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Ticket;

#[Route('/tickets')]
class TicketController extends AbstractController
{

    #[Route('/', name: "tickets_list")]
    public function list(ManagerRegistry $doctrine): Response
    {
        $tickets = $doctrine->getRepository(Ticket::class)->findAll();
        // dd($tickets);

        return $this->render('tickets/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/{ticketId}', name: "tickets_show")]
    public function show(?int $ticketId, ManagerRegistry $doctrine): Response
    {
        $ticket = $doctrine->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException(
                'No ticket found for id ' . $ticketId
            );
        }

        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
}