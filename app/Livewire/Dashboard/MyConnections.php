<?php

namespace App\Livewire\Dashboard;

use App\Exceptions\ConnectionActionException;
use App\Models\Agency;
use App\Services\ConnectionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyConnections extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'received';

    public ?string $error = null;

    public bool $showForm = false;

    public string $property_id = '';

    public string $agency_id = '';

    public string $message = '';

    public ?string $formError = null;

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

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

    public function sendRequest(): void
    {
        $validated = $this->validate([
            'property_id' => 'required|integer',
            'agency_id' => 'required|integer',
            'message' => 'nullable|string',
        ]);

        $property = auth()->user()->properties()->findOrFail($validated['property_id']);
        $agency = Agency::findOrFail($validated['agency_id']);

        try {
            app(ConnectionService::class)->initiate(auth()->user(), $property, $agency, $validated['message'] ?: null);
            $this->reset('property_id', 'agency_id', 'message', 'showForm');
            $this->formError = null;
            $this->setTab('sent');
        } catch (ConnectionActionException $e) {
            $this->formError = $e->getMessage();
        }
    }

    public function render()
    {
        $connections = app(ConnectionService::class);

        $query = $this->tab === 'sent'
            ? $connections->sentByMe(auth()->user())
            : $connections->mine(auth()->user());

        return view('livewire.dashboard.my-connections', [
            'connections' => $query->with(['property', 'agency'])->latest()->paginate(10),
            'properties' => auth()->user()->properties()->orderBy('title')->get(),
            'agencies' => Agency::orderBy('name')->limit(50)->get(),
        ]);
    }
}
