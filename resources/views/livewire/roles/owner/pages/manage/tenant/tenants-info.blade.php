<?php

use App\Models\Tenant;
use App\Models\User; // Added User model
use App\Models\Property;
use App\Models\Company;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

new class extends Component {
  use Toast;
  use WithPagination;

  public string $search = "";
  public bool $drawer = false;
  public array $sortBy = ["column" => "user_last_name", "direction" => "asc"]; // Updated column name

  public ?int $company_id = null;
  public ?int $property_id = null;
  public array $companies = [];
  public array $properties = [];

  public function clear(): void
  {
    $this->reset();
    $this->resetPage();
    $this->success("Filters cleared.", position: "toast-bottom");
  }

  // Delete action
  public function deleteTenant(int $tenantId): void
  {
    // Find the tenant by ID
    $tenant = Tenant::find($tenantId);

    if ($tenant) {
      // Get the associated user account
      $user = User::find($tenant->user_id);

      // Delete the user account if it exists
      if ($user) {
        $user->delete();
      }

      // Delete the tenant record
      $tenant->delete();

      // Provide feedback to the user
      $this->success('Tenant and user account deleted successfully.', redirectTo: '/tenants-information');
    } else {
      $this->error('Tenant not found.');
    }
  }

  // Table headers
  public function headers(): array
  {
    return [
      ["key" => "user_avatar", "label" => "Profile", "class" => "w-1"],

      ["key" => "user_first_name", "label" => "First Name", "class" => ""],
      ["key" => "user_last_name", "label" => "Last Name", "class" => ""],

      // Remove property column
      // ["key" => "property_name", "label" => "Property", "class" => ""],
      ["key" => "user_username", "label" => "Username", "class" => ""], // Added user name column
      ["key" => "user_email", "label" => "User Email", "class" => "hidden"], // Added user email column
      ["key" => "image", "label" => "", "class" => "w-1"],

      ["key" => "created_at", "label" => "Created at", "class" => ""],
      ["key" => "updated_at", "label" => "Updated at", "class" => ""],
    ];
  }

  public function mount()
  {
    $this->companies = Company::all()->map(function ($company) {
      return [
        'id' => $company->id,
        'name' => $company->name,
      ];
    })->toArray();
    $this->updateProperties();
  }

  public function updatedCompanyId()
  {
    $this->updateProperties();
    $this->property_id = null; // Reset property selection when company changes
    $this->resetPage();
  }

  public function updateProperties()
  {
    $propertyQuery = Property::query();
    if ($this->company_id) {
      $propertyQuery->where('company_id', $this->company_id);
    }
    $this->properties = $propertyQuery->get()->map(function ($property) {
      return [
        'id' => $property->id,
        'name' => $property->apartment_no . ' - ' . $property->name,
      ];
    })->toArray();
  }

  public function tenants(): LengthAwarePaginator
  {
    return Tenant::query()
      ->withAggregate("user", "avatar")
      ->withAggregate("user", "username")
      ->withAggregate("user", "first_name")
      ->withAggregate("user", "last_name")
      ->withAggregate("user", "email")
      ->with(["user", "bedAssignments.bed.room.property.company"])
      ->when($this->search, fn(Builder $q) => $q->whereHas("user", fn(Builder $q) => $q->where(function (Builder $q) {
        $q->where("first_name", "like", "%$this->search%")
          ->orWhere("middle_name", "like", "%$this->search%")
          ->orWhere("last_name", "like", "%$this->search%");
      })))
      ->when($this->company_id, function (Builder $q) {
        $q->whereHas('bedAssignments.bed.room.property.company', function ($query) {
          $query->where('id', $this->company_id);
        });
      })
      ->when($this->property_id, function (Builder $q) {
        $q->whereHas('bedAssignments.bed.room.property', function ($query) {
          $query->where('id', $this->property_id);
        });
      })
      ->orderBy(...array_values($this->sortBy))
      ->paginate(4);
  }

  public function with(): array
  {
    return [
      "tenants" => $this->tenants(),
      "headers" => $this->headers(),
      'companies' => $this->companies,
      'properties' => $this->properties,
      'company_id' => $this->company_id,
      'property_id' => $this->property_id,
    ];
  }

  public function updated($value): void
  {
    if (! is_array($value) && $value != "") {
      $this->resetPage();
    }
  }

  public function activeFiltersCount(): int
  {
    $count = 0;

    if ($this->search) {
      $count++;
    }

    // Remove property filter count
    // if ($this->property_id) {
    //   $count++;
    // }

    return $count;
  }
}; ?>

<div>
  <!-- HEADER -->

  <x-header title="Tenant" separator progress-indicator>
    <x-slot:middle class="!justify-end">
      <x-input placeholder="Tenant..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
      </x-slot>
      <x-slot:actions>
        <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}" @click="$wire.drawer = true" responsive icon="o-funnel" />
        <x-button class="btn normal-case btn-primary" label="Create" link="/create-tenant" responsive icon="o-plus" class="btn-primary" />
        </x-slot>
  </x-header>

  <!-- TABLE -->
  <x-card>
    <x-table :headers="$headers" :rows="$tenants" :sort-by="$sortBy" with-pagination link="tenant/{id}/view?name={user_first_name}+{user_last_name}">
      @scope("cell_user_avatar", $user)
      <x-avatar image="{{ $user->user_avatar ?? '/empty-user.jpg' }}" class="!w-14 rounded-lg" />
      @endscope


      @scope("actions", $user)
      <x-button icon="o-trash" wire:click="deleteTenant({{ $user->id  }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
      @endscope

      {{--
        @scope('cell_user_name', $tenant)
        {{ $tenant->user->name }}
      @endscope
      @scope('cell_user_email', $tenant)
      {{ $tenant->user->email }}
      @endscope
      --}}
      <x-slot:empty>
        <x-icon name="o-cube" label="It is empty." />
        </x-slot>
    </x-table>
  </x-card>

  <!-- FILTER DRAWER -->
  <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
    <div class="grid gap-5">
      <x-input placeholder="Tenant..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
      <x-select label="Company" wire:model.live="company_id" :options="$companies" option-label="name" option-value="id" placeholder="All Companies" />
      <x-select label="Property" wire:model.live="property_id" :options="$properties" option-label="name" option-value="id" placeholder="All Properties" />
    </div>
    <x-slot:actions>
      <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
      <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
      </x-slot>
  </x-drawer>
</div>