<?php

namespace App\Livewire\Admin\TicketLinks;

use App\Models\Event;
use App\Models\TicketLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TicketLinkTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $eventId = '';
    public string $saleChannel = '';
    public string $status = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public array $form = [
        'event_id' => '',
        'provider_name' => '',
        'label' => '',
        'sale_channel' => 'external',
        'url' => '',
        'price_from' => '',
        'currency' => 'BOB',
        'starts_at' => '',
        'ends_at' => '',
        'display_order' => 0,
        'status' => 1,
    ];

    public function mount(): void
    {
        Gate::authorize('ticket_links.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEventId(): void
    {
        $this->resetPage();
    }

    public function updatingSaleChannel(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        Gate::authorize('ticket_links.create');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('ticket_links.update');

        $ticketLink = TicketLink::query()->findOrFail($id);

        $this->editingId = $ticketLink->id;
        $this->form = [
            'event_id' => (string) $ticketLink->event_id,
            'provider_name' => $ticketLink->provider_name,
            'label' => $ticketLink->label,
            'sale_channel' => $ticketLink->sale_channel,
            'url' => $ticketLink->url,
            'price_from' => $ticketLink->price_from !== null ? (string) $ticketLink->price_from : '',
            'currency' => $ticketLink->currency ?? 'BOB',
            'starts_at' => $this->dateTimeLocal($ticketLink->starts_at),
            'ends_at' => $this->dateTimeLocal($ticketLink->ends_at),
            'display_order' => (int) $ticketLink->display_order,
            'status' => (int) $ticketLink->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        Gate::authorize($this->editingId ? 'ticket_links.update' : 'ticket_links.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];

        if (! $this->validateDateWindow($validated)) {
            return;
        }

        $ticketLink = $this->editingId
            ? TicketLink::query()->findOrFail($this->editingId)
            : new TicketLink();

        $this->normalizeTicketLinkFields($validated);
        $ticketLink->fill($validated);
        $ticketLink->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.ticket_links.messages.updated')
                : __('mma.admin.ticket_links.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('ticket_links.delete');

        $ticketLink = TicketLink::query()->findOrFail($id);

        $this->pendingDeleteId = $ticketLink->id;
        $this->deleteName = $ticketLink->label;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Gate::authorize('ticket_links.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        TicketLink::query()->findOrFail($this->pendingDeleteId)->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.ticket_links.messages.deleted')]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->deleteName = '';
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'eventId', 'saleChannel', 'status']);
        $this->resetPage();
    }

    public function saleChannelOptions(): array
    {
        return [
            'external' => __('mma.admin.ticket_links.sale_channels.external'),
            'whatsapp' => __('mma.admin.ticket_links.sale_channels.whatsapp'),
            'phone' => __('mma.admin.ticket_links.sale_channels.phone'),
            'streaming' => __('mma.admin.ticket_links.sale_channels.streaming'),
            'vip' => __('mma.admin.ticket_links.sale_channels.vip'),
            'other' => __('mma.admin.ticket_links.sale_channels.other'),
        ];
    }

    public function currencyOptions(): array
    {
        return ['BOB' => 'BOB', 'USD' => 'USD'];
    }

    public function saleChannelLabel(?string $channel): string
    {
        return $channel ? ($this->saleChannelOptions()[$channel] ?? $channel) : __('mma.admin.common.not_available');
    }

    public function statusLabel(int $status): string
    {
        return $status === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    public function render()
    {
        $ticketLinks = TicketLink::query()
            ->with('event:id,name,slug,starts_at,status')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('provider_name', 'like', "%{$this->search}%")
                        ->orWhere('label', 'like', "%{$this->search}%")
                        ->orWhere('url', 'like', "%{$this->search}%")
                        ->orWhereHas('event', function ($eventQuery) {
                            $eventQuery->where('name', 'like', "%{$this->search}%")
                                ->orWhere('slug', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->eventId !== '', fn ($query) => $query->where('event_id', (int) $this->eventId))
            ->when($this->saleChannel !== '', fn ($query) => $query->where('sale_channel', $this->saleChannel))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->orderBy('display_order')
            ->latest()
            ->paginate($this->perPage);

        $events = Event::query()
            ->select('id', 'name', 'slug', 'starts_at', 'status')
            ->orderByDesc('starts_at')
            ->get();

        return view('livewire.admin.ticket-links.ticket-link-table', compact('ticketLinks', 'events'));
    }

    protected function rules(): array
    {
        return [
            'form.event_id' => ['required', 'integer', 'exists:events,id'],
            'form.provider_name' => ['required', 'string', 'max:120'],
            'form.label' => ['required', 'string', 'max:120'],
            'form.sale_channel' => ['required', Rule::in(array_keys($this->saleChannelOptions()))],
            'form.url' => ['required', 'url', 'max:500'],
            'form.price_from' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'form.currency' => ['nullable', Rule::in(array_keys($this->currencyOptions()))],
            'form.starts_at' => ['nullable', 'date'],
            'form.ends_at' => ['nullable', 'date'],
            'form.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'form.status' => ['required', Rule::in([0, 1])],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.event_id' => __('mma.admin.ticket_links.form.event_id'),
            'form.provider_name' => __('mma.admin.ticket_links.form.provider_name'),
            'form.label' => __('mma.admin.ticket_links.form.label'),
            'form.sale_channel' => __('mma.admin.ticket_links.form.sale_channel'),
            'form.url' => __('mma.admin.ticket_links.form.url'),
            'form.price_from' => __('mma.admin.ticket_links.form.price_from'),
            'form.currency' => __('mma.admin.ticket_links.form.currency'),
            'form.starts_at' => __('mma.admin.ticket_links.form.starts_at'),
            'form.ends_at' => __('mma.admin.ticket_links.form.ends_at'),
            'form.display_order' => __('mma.admin.ticket_links.form.display_order'),
            'form.status' => __('mma.admin.ticket_links.form.status'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'event_id' => '',
            'provider_name' => '',
            'label' => '',
            'sale_channel' => 'external',
            'url' => '',
            'price_from' => '',
            'currency' => 'BOB',
            'starts_at' => '',
            'ends_at' => '',
            'display_order' => 0,
            'status' => 1,
        ];
        $this->resetErrorBag();
    }

    protected function normalizeTicketLinkFields(array &$data): void
    {
        foreach (['price_from', 'currency', 'starts_at', 'ends_at'] as $field) {
            if (($data[$field] ?? null) === '') {
                $data[$field] = null;
            }
        }

        $data['event_id'] = (int) $data['event_id'];
        $data['price_from'] = $data['price_from'] !== null ? round((float) $data['price_from'], 2) : null;
        $data['starts_at'] = $data['starts_at'] ? Carbon::parse($data['starts_at']) : null;
        $data['ends_at'] = $data['ends_at'] ? Carbon::parse($data['ends_at']) : null;
        $data['display_order'] = (int) $data['display_order'];
        $data['status'] = (int) $data['status'];
    }

    protected function validateDateWindow(array $data): bool
    {
        if (($data['starts_at'] ?? '') === '' || ($data['ends_at'] ?? '') === '') {
            return true;
        }

        if (Carbon::parse($data['ends_at'])->lt(Carbon::parse($data['starts_at']))) {
            $this->addError('form.ends_at', __('mma.admin.ticket_links.validation.ends_after_start'));

            return false;
        }

        return true;
    }

    protected function dateTimeLocal($dateTime): string
    {
        return $dateTime ? $dateTime->format('Y-m-d\TH:i') : '';
    }
}
