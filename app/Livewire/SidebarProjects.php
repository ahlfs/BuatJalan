<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class SidebarProjects extends Component
{
    public ?string $currentSlug = null;

    /**
     * Mount the component.
     *
     * @param string|null $currentSlug
     */
    public function mount(?string $currentSlug = null)
    {
        $this->currentSlug = $currentSlug;
    }

    /**
     * Event listener to refresh the sidebar dynamically.
     */
    #[On('refresh-sidebar')]
    public function refresh()
    {
        // This triggers a Livewire re-render automatically.
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.sidebar-projects');
    }
}
