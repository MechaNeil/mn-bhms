<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Notice;

new 
#[Layout('components.layouts.tenant')] 
#[Title('Notice Board')]

 class extends Component {
    public $title = "";
    public $noticeBody = "";
    public $notice;
    public $config = [
        'plugins' => 'autoresize',
        'min_height' => 150,
        'max_height' => 250,
        'statusbar' => false,
        'toolbar' => false,
        'quickbars_selection_toolbar' => 'bold italic link',
    ];
    public function mount()
    {
        $this->notice = Notice::find(1);
        if ($this->notice) {
            $this->title = $this->notice->title;
            $this->noticeBody = $this->notice->body;
        }
    }


    // Remove the save method
}; ?>


<div>
    <x-header title="Notice Board" subtitle="Announcement">

    </x-header>

    <!-- Remove the form submission -->
    <div class="lg:grid grid-cols-5">
        <div class="col-span-2">
            <div class="hidden lg:block">
                <livewire:roles.owner.pages.notification.notice.notice-image>
            </div>
        </div>

        <div class="col-span-3 grid  ">
            <x-header title="{{ $title }}" >

            </x-header>
            <x-editor wire:model="noticeBody"  :config="$config" label="Notice Body" hint="Read This" readonly />
        </div>
        <!-- Remove the save button -->
    </div>
</div>

