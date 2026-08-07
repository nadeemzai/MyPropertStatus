<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SupportTickets extends Component
{
    use WithPagination;

    public string $subject = '';

    public string $message = '';

    public bool $showForm = false;

    public function createTicket(): void
    {
        $validated = $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        auth()->user()->supportTickets()->create($validated);

        $this->reset('subject', 'message', 'showForm');
    }

    public function render()
    {
        $tickets = auth()->user()->supportTickets()->latest()->paginate(10);

        return view('livewire.dashboard.support-tickets', [
            'tickets' => $tickets,
        ]);
    }
}
