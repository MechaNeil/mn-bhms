<?php

// permission.blade.php
// This Livewire Volt component manages the assignment of permissions to roles in the user management section for owners.
// It provides functionality to search, display, add, edit, and delete role-permission relationships.

use App\Models\Role; // Role model for role data
use App\Models\Permission; // Permission model for permission data
use Illuminate\Database\Eloquent\Builder; // Eloquent builder for query customization
use Livewire\Volt\Component; // Base class for Volt components
use Livewire\WithPagination; // Trait for pagination support

// Anonymous Volt component class for role-permission management
new class extends Component {
    use WithPagination; // Enables pagination for data tables

    // Search query for filtering roles
    public string $search = '';
    // Sorting configuration for the table
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    // Controls the visibility of the add/edit modal
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showPermissionsModal = false;
    // Form data for role and permissions selection
    public array $createForm = [
        'role_name' => '', // changed from role_id
        'permission_ids' => [],
    ];
    public array $editForm = [
        'role_id' => '',
        'permission_ids' => [],
    ];
    public array $permissionForm = [
        'name' => '',
    ];
    public $selectedPermissionId = null;

    /**
     * Resets the component state and pagination.
     */
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
    }

    /**
     * Detaches a permission from a role.
     * @param int $roleId
     * @param int $permissionId
     */
    public function delete($roleId, $permissionId): void
    {
        $role = Role::find($roleId);
        if ($role) {
            $role->permissions()->detach($permissionId);
        }
    }

    /**
     * Returns table headers for the role-permission table.
     * @return array
     */
    public function headers(): array
    {
        return [
            ['key' => 'role_name', 'label' => 'Role Name', 'class' => 'w-12'],
            ['key' => 'permission_names', 'label' => 'Permission Name', 'class' => 'w-12']
        ];
    }

    /**
     * Retrieves roles with their associated permissions, filtered and sorted.
     * @return \Illuminate\Support\Collection
     */
    public function groupedRolePermissions()
    {
        return Role::query()
            ->with(['permissions' => function ($q) {
                $q->select('permissions.id', 'permissions.name');
            }])
            ->when($this->search, function (Builder $q) {
                $q->where('name', 'like', "%$this->search%");
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->limit(5)
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'role_name' => $role->name,
                    'permissions' => $role->permissions,
                    'permission_names' => $role->permissions->pluck('name')->join(', '),
                ];
            });
    }

    /**
     * Provides data to the Blade view, including roles, permissions, and table headers.
     * @return array
     */
    public function with(): array
    {
        return [
            'rolePermissions' => $this->groupedRolePermissions(),
            'headers' => $this->headers(),
            'allRoles' => Role::all(),
            'allPermissions' => Permission::all(),
        ];
    }

    /**
     * Resets pagination when a non-array property is updated.
     * @param mixed $value
     */
    public function updated($value): void
    {
        if (!is_array($value) && $value != '') {
            $this->resetPage();
        }
    }

    /**
     * Opens the modal for creating a new role's permissions, resetting the form.
     */
    public function openCreateModal(): void
    {
        $this->createForm['role_name'] = '';
        $this->createForm['permission_ids'] = [];
        $this->showCreateModal = true;
    }

    /**
     * Opens the modal for editing a role's permissions, pre-filling the form.
     * @param int $roleId
     */
    public function openEditModal($roleId): void
    {
        $role = Role::find($roleId);
        if ($role) {
            $this->editForm['role_id'] = $role->id;
            $this->editForm['permission_ids'] = $role->permissions->pluck('id')->toArray();
            $this->showEditModal = true;
        }
    }

    /**
     * Opens the modal for managing permissions, resetting the form.
     */
    public function openPermissionsModal(): void
    {
        $this->permissionForm['name'] = '';
        $this->selectedPermissionId = null;
        $this->showPermissionsModal = true;
    }

    /**
     * Validates and saves the role-permission assignments from the form.
     */
    public function saveCreateRolePermission(): void
    {
        $this->validate([
            'createForm.role_name' => 'required|string|unique:roles,name',
            'createForm.permission_ids' => 'required|array',
        ]);
        $role = Role::create(['name' => $this->createForm['role_name']]);
        if ($role) {
            $role->permissions()->sync($this->createForm['permission_ids']);
        }
        $this->showCreateModal = false;
        $this->clear();
    }

    /**
     * Saves the new permission to the database.
     */
    public function savePermission(): void
    {
        $this->validate([
            'permissionForm.name' => 'required|string|unique:permissions,name',
        ]);
        Permission::create(['name' => $this->permissionForm['name']]);
        $this->permissionForm['name'] = '';
        $this->selectedPermissionId = null;
        // Optionally, refresh permissions list
    }

    public function saveEditRolePermission(): void
    {
        $this->validate([
            'editForm.role_id' => 'required|exists:roles,id',
            'editForm.permission_ids' => 'required|array',
        ]);
        $role = Role::find($this->editForm['role_id']);
        if ($role) {
            $role->permissions()->sync($this->editForm['permission_ids']);
        }
        $this->showEditModal = false;
        $this->clear();
    }

    public function deletePermission($permissionId): void
    {
        $permission = Permission::find($permissionId);
        if ($permission) {
            $permission->delete();
        }
        // Optionally, refresh permissions list
    }
}; ?>

<div>
    <!-- HEADER -->
    <!--
        Header section with search input and button to open the add/edit role permission modal.
        Uses x-header, x-input, and x-button Blade components.
    -->
    <x-header title="Role Permissions" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
            <x-slot:actions>
                <x-button class="ml-2" wire:click="openPermissionsModal">Permissions</x-button>
                <x-button class="ml-2" wire:click="openCreateModal">Create Role</x-button>
            </x-slot:actions>
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <!--
        Table displaying roles and their permissions.
        Each row shows the role name, its permissions, and action buttons for editing or deleting permissions.
        Uses x-card and x-table Blade components.
    -->
    <x-card>
        <x-table :headers="$headers" :rows="$rolePermissions" :sort-by="$sortBy" @row-click="$wire.openEditModal($event.detail.id)">
            @foreach($rolePermissions as $role)
            <tr>
                <td>{{ $role['role_name'] }}</td>
                <td>
                    @foreach($role['permissions'] as $permission)
                    <span class="inline-flex items-center">
                        {{ $permission->name }}
                        <x-button icon="o-trash"
                            wire:click="delete({{ $role['id'] }}, {{ $permission->id }})"
                            wire:confirm="Are you sure?"
                            spinner
                            class="btn-ghost btn-xs text-red-500 ml-1" />
                    </span>
                    @if(!$loop->last), @endif
                    @endforeach
                </td>
                <td>
                    <x-button icon="o-pencil" wire:click="openEditModal({{ $role['id'] }})" class="btn-ghost btn-xs text-blue-500 ml-1" />
                </td>
            </tr>
            @endforeach
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- Modal for Create Role -->
    <!--
        Modal dialog for assigning permissions to a new role.
        Contains a form with role and permissions selection, and actions to cancel or save.
        Uses x-modal, x-form, x-select, and x-button Blade components.
    -->
    <x-modal wire:model="showCreateModal" title="Create Role" subtitle="Assign permissions to a new role" separator>
        <x-form wire:submit="saveCreateRolePermission">
            <x-input label="Role Name" wire:model.blur="createForm.role_name" placeholder="Enter role name" />
            <x-choices label="Permissions" wire:model.blur="createForm.permission_ids" :options="$allPermissions" option-label="name" option-value="id" multiple placeholder="Select permissions" clearable />
            
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.showCreateModal = false" />
                <x-button label="Save" class="btn-primary" type="submit" spinner="saveCreateRolePermission" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <!-- Modal for Edit Role Permission -->
    <x-modal wire:model="showEditModal" title="Edit Role Permission" subtitle="Manage role permissions" separator>
        <x-form wire:submit="saveEditRolePermission">
            <x-select label="Role" wire:model.blur="editForm.role_id" :options="$allRoles" option-label="name" option-value="id" placeholder="Select a role" />
            
            <x-choices label="Permissions" wire:model.blur="editForm.permission_ids" :options="$allPermissions" option-label="name" option-value="id" multiple placeholder="Select permissions" clearable />
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.showEditModal = false" />
                <x-button label="Save" class="btn-primary" type="submit" spinner="saveEditRolePermission" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <!-- Modal for Permissions Management -->
    <x-modal wire:model="showPermissionsModal" title="Permissions" subtitle="Manage permissions" separator>
        <div class="mb-4">
            <x-form wire:submit="savePermission">
                <x-input label="Permission Name" wire:model.blur="permissionForm.name" placeholder="Enter permission name" />
                <x-slot:actions>
                    <x-button label="Add" class="btn-primary ml-2 " type="submit" spinner="savePermission" />
                </x-slot:actions>
            </x-form>
        </div>
        <div>
            <h4 class="font-semibold mb-2">Existing Permissions</h4>
            <ul>
                @foreach($allPermissions as $permission)
                <li class="flex items-center justify-between mb-1">
                    <span>{{ $permission->name }}</span>
                    <x-button icon="o-trash" wire:click="deletePermission({{ $permission->id }})" class="btn-ghost btn-xs text-red-500 ml-2" wire:confirm="Are you sure you want to delete this permission?" />
                </li>
                @endforeach
            </ul>
        </div>
        <x-slot:actions>
            <x-button label="Close" @click="$wire.showPermissionsModal = false" />
        </x-slot:actions>
    </x-modal>
</div>