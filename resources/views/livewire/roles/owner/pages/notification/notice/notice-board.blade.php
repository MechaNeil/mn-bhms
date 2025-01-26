<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Models\Notice;

new class extends Component {
    use Toast;



    public $title = '';
    public $noticeBody = '';
    public $notice;
    public $config = [
        'plugins' => 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount autoresize',
        'min_height' => 250,
        'max_height' => 700,
        'resize' => 'both',
        
        'toolbar' => 'undo redo | blocks | ' . 'bold italic backcolor | alignleft aligncenter ' . 'alignright alignjustify | bullist numlist outdent indent | ' . 'removeformat quickimage | help ' ,
        'quickbars_selection_toolbar' => 'bold italic link',
        'content_style' => 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
    ];

    public function mount()
    {
        $this->notice = Notice::find(1);
        if ($this->notice) {
            $this->title = $this->notice->title;
            $this->noticeBody = $this->notice->body;
        }
    }
    public function clear()
    {
        $this->reset(['title', 'noticeBody']);
    }

    public function save()
    {
        if ($this->notice) {
            $this->notice->update([
                'title' => $this->title,
                'body' => $this->noticeBody,
            ]);
        } else {
            Notice::create([
                'title' => $this->title,
                'body' => $this->noticeBody,
            ]);
        }
        $this->success('Notice successfuly updated.', position: 'toast-top');

        // $this->reset(['title', 'noticeBody']);
    }
}; ?>

<div>
    <x-header title="Notice Board" separator>
        <x-slot:actions>
            <x-button label="Clear" icon="o-trash" spinner class="btn-warning" wire:click="clear" />
        </x-slot:actions>
    </x-header>

    <x-form wire:submit="save">
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <div class="hidden lg:block">
                    <livewire:roles.owner.pages.notification.notice.notice-image>
                </div>
            </div>

            <div class="col-span-3 grid gap-3 ">
                <x-input wire:model="title" label="Title" placeholder="Title" />
                <x-editor wire:model="noticeBody" :config="$config" label="Notice Body" hint="Say Something" />
            </div>
            <x-slot:actions>
                <x-button type="submit" label="Save" class="btn-primary" />

            </x-slot:actions>
        </div>
    </x-form>
</div>
