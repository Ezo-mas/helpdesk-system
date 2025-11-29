<?php

namespace App\Controllers;

use App\Models\Ticket;
use App\Models\User;

class TicketController
{
    public function createTicket($data)
    {
        $ticket = new Ticket();
        $ticket->setTitle($data['title']);
        $ticket->setDescription($data['description']);
        $ticket->setUserId($data['user_id']);
        return $ticket->save();
    }

    public function viewTicket($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        return $ticket;
    }

    public function updateTicket($ticketId, $data)
    {
        $ticket = Ticket::find($ticketId);
        if ($ticket) {
            $ticket->setTitle($data['title']);
            $ticket->setDescription($data['description']);
            return $ticket->save();
        }
        return false;
    }

    public function deleteTicket($ticketId)
    {
        $ticket = Ticket::find($ticketId);
        if ($ticket) {
            return $ticket->delete();
        }
        return false;
    }

    public function listTicketsByUser($userId)
    {
        return Ticket::where('user_id', $userId)->get();
    }

    public function listAllTickets()
    {
        return Ticket::all();
    }
}