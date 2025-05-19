<?php


use Livewire\Volt\Component;


new class extends Component {



}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Bills" separator progress-indicator>
    </x-header>

    <!-- UTILITY BILLS TABLE -->
    <livewire:roles.owner.pages.invoice.utility-bills.utility-bills-table />

    <!-- CONSTANT UTILITY BILLS TABLE -->
    <livewire:roles.owner.pages.invoice.utility-bills.constant-utility-bills-table />
</div>