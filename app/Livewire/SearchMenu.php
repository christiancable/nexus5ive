<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Helpers\TreeHelper;

class SearchMenu extends Component
{
    public $searchTerm;

    public $showRecent = true;

    public $locations;

    public function mount()
    {
        $this->fetchTree();
    }

    public function fetchTree()
    {
        $this->locations = collect(Cache::rememberForever('tree', function () {
            return TreeHelper::tree();
        }));
    }

    public function performSearch()
    {
        if (! empty($this->searchTerm)) {
            return redirect('/search/'.$this->searchTerm);
        }
    }

    #[Computed]
    public function matchedLocations()
    {
        $results = collect();

        if ($this->showRecent) {
            $results = $this->locations->filter(function ($item) {
                return $item['is_recent'] === true;
            });
        } else {
            $results = $this->locations;
        }

        if (strlen($this->searchTerm) > 2) {
            $results = $results->filter(function ($item) {
                return Str::contains($item['title'].' '.$item['intro'], $this->searchTerm, ignoreCase: true);
            });
        }

        return $results;
    }

    public function render()
    {
        return view('livewire.search-menu');
    }
}
