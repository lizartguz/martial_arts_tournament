<?php

namespace App\Livewire\Catalog;

use App\Models\BrandM;
use App\Models\StationModelM;
use App\Support\ErrorMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BrandCatalog extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Vista activa: 'brands' | 'models'
    public string $view = 'brands';

    public int $perPage = 10;
    public string $search = '';

    // Marca seleccionada (al entrar a sus modelos)
    public ?int $selectedBrandId = null;
    public string $selectedBrandName = '';

    // Formulario de Marca
    public bool $showBrandForm = false;
    public ?int $editingBrandId = null;
    public string $brandName = '';
    public int $brandState = 1;

    // Formulario de Modelo
    public bool $showModelForm = false;
    public ?int $editingModelId = null;
    public string $modelName = '';
    public int $modelState = 1;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    // ─── MARCAS ───────────────────────────────────────────────────────────────

    public function openBrandForm(): void
    {
        $this->resetBrandForm();
        $this->showBrandForm = true;
    }

    public function editBrand(int $id): void
    {
        $brand = BrandM::findOrFail($id);
        $this->editingBrandId = $brand->id;
        $this->brandName      = $brand->name;
        $this->brandState     = (int) $brand->state;
        $this->resetErrorBag();
        $this->showBrandForm  = true;
    }

    public function saveBrand(): void
    {
        $this->validate([
            'brandName'  => ['required', 'string', 'max:80',
                Rule::unique('brands', 'name')->ignore($this->editingBrandId),
            ],
            'brandState' => ['required', 'in:0,1'],
        ]);

        try {
            DB::beginTransaction();

            BrandM::updateOrCreate(
                ['id' => $this->editingBrandId],
                ['name' => trim($this->brandName), 'state' => $this->brandState]
            );

            DB::commit();

            $msg = $this->editingBrandId
                ? __('messages.catalog.brands.messages.updated')
                : __('messages.catalog.brands.messages.created');

            $this->dispatch('successAlert', ['success' => $msg]);
            $this->closeBrandForm();

        } catch (\Throwable $th) {
            DB::rollBack();
            $key = $this->editingBrandId ? 'brand_update' : 'brand_create';
            $this->dispatch('failedAlert', [
                'failed' => ErrorMessage::forUser($th, __("messages.operation_errors.{$key}")),
            ]);
        }
    }

    public function deleteBrand(int $id): void
    {
        $brand = BrandM::withCount('stationModels')->findOrFail($id);

        if ($brand->station_models_count > 0) {
            $this->dispatch('failedAlert', [
                'failed' => __('messages.catalog.brand_has_models'),
            ]);
            return;
        }

        try {
            DB::beginTransaction();
            $brand->delete();
            DB::commit();
            $this->dispatch('successAlert', ['success' => __('messages.catalog.brands.messages.deleted')]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert', [
                'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.brand_delete')),
            ]);
        }
    }

    public function closeBrandForm(): void
    {
        $this->showBrandForm = false;
        $this->resetBrandForm();
    }

    public function viewModels(int $brandId): void
    {
        $brand = BrandM::findOrFail($brandId);
        $this->selectedBrandId   = $brand->id;
        $this->selectedBrandName = $brand->name;
        $this->view              = 'models';
        $this->search            = '';
        $this->resetPage();
    }

    // ─── MODELOS ──────────────────────────────────────────────────────────────

    public function backToBrands(): void
    {
        $this->view              = 'brands';
        $this->selectedBrandId   = null;
        $this->selectedBrandName = '';
        $this->search            = '';
        $this->closeModelForm();
        $this->resetPage();
    }

    public function openModelForm(): void
    {
        $this->resetModelForm();
        $this->showModelForm = true;
    }

    public function editModel(int $id): void
    {
        $model = StationModelM::findOrFail($id);
        $this->editingModelId = $model->id;
        $this->modelName      = $model->name;
        $this->modelState     = (int) $model->state;
        $this->resetErrorBag();
        $this->showModelForm  = true;
    }

    public function saveModel(): void
    {
        $this->validate([
            'modelName'  => ['required', 'string', 'max:80',
                Rule::unique('station_models', 'name')
                    ->where('brand_id', $this->selectedBrandId)
                    ->ignore($this->editingModelId),
            ],
            'modelState' => ['required', 'in:0,1'],
        ]);

        try {
            DB::beginTransaction();

            StationModelM::updateOrCreate(
                ['id' => $this->editingModelId],
                [
                    'name'     => trim($this->modelName),
                    'state'    => $this->modelState,
                    'brand_id' => $this->selectedBrandId,
                ]
            );

            DB::commit();

            $msg = $this->editingModelId
                ? __('messages.catalog.models.messages.updated')
                : __('messages.catalog.models.messages.created');

            $this->dispatch('successAlert', ['success' => $msg]);
            $this->closeModelForm();

        } catch (\Throwable $th) {
            DB::rollBack();
            $key = $this->editingModelId ? 'model_update' : 'model_create';
            $this->dispatch('failedAlert', [
                'failed' => ErrorMessage::forUser($th, __("messages.operation_errors.{$key}")),
            ]);
        }
    }

    public function deleteModel(int $id): void
    {
        $model = StationModelM::withCount('detailStations')->findOrFail($id);

        if ($model->detail_stations_count > 0) {
            $this->dispatch('failedAlert', [
                'failed' => __('messages.catalog.model_in_use'),
            ]);
            return;
        }

        try {
            DB::beginTransaction();
            $model->delete();
            DB::commit();
            $this->dispatch('successAlert', ['success' => __('messages.catalog.models.messages.deleted')]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('failedAlert', [
                'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.model_delete')),
            ]);
        }
    }

    public function closeModelForm(): void
    {
        $this->showModelForm = false;
        $this->resetModelForm();
    }

    // ─── COMPUTED ─────────────────────────────────────────────────────────────

    public function getBrandsProperty()
    {
        return BrandM::withCount('stationModels')
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage, ['*'], 'brandsPage');
    }

    public function getModelsProperty()
    {
        return StationModelM::withCount('detailStations')
            ->where('brand_id', $this->selectedBrandId)
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage, ['*'], 'modelsPage');
    }

    // ─── RENDER ───────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.catalog.brand-catalog', [
            'brands' => $this->view === 'brands' ? $this->brands : null,
            'models' => $this->view === 'models' ? $this->models : null,
        ]);
    }

    // ─── HELPERS PRIVADOS ─────────────────────────────────────────────────────

    protected function resetBrandForm(): void
    {
        $this->editingBrandId = null;
        $this->brandName      = '';
        $this->brandState     = 1;
        $this->resetErrorBag();
    }

    protected function resetModelForm(): void
    {
        $this->editingModelId = null;
        $this->modelName      = '';
        $this->modelState     = 1;
        $this->resetErrorBag();
    }
}
