<?php

namespace App\Livewire\Dashboard;

use App\Exceptions\ConnectionActionException;
use App\Services\ConnectionService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyConnections extends Component
{
    use WithPagination;

    public ?string $error = null;

    public function accept(int $connectionId): void
    {
        $this->respond($connectionId, true);
    }

    public function reject(int $connectionId): void
    {
        $this->respond($connectionId, false);
    }

    private function respond(int $connectionId, bool $accepted): void
    {
        $connections = app(ConnectionService::class);
        $connection = $connections->mine(auth()->user())->with('property')->findOrFail($connectionId);

        try {
            $accepted ? $connections->accept($connection, auth()->user()) : $connections->reject($connection, auth()->user());
            $this->error = null;
        } catch (ConnectionActionException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        $connections = app(ConnectionService::class)->mine(auth()->user())
            ->with(['property', 'agency'])
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.my-connections', [
            'connections' => $connections,
        ]);
    }
}
