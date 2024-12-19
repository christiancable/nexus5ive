<ul class="nav nav-pills justify-content-end mb-1 cog-menu {{ $forceCogMenu ?? 'd-none' }}">

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true"
            aria-expanded="false">
            <x-heroicon-o-cog-6-tooth class="icon_mini" aria-hidden="true" /><span class="visually-hidden">Settings</span>
        </a>

        <div class="dropdown-menu dropdown-menu-end">

            <a class="dropdown-item" href="{{ $viewTabLink }}" id="{{ $viewTabId }}" data-bs-toggle="tab"
                role="tab" aria-controls="{{ $viewTabId }}">View</a>

            <a class="dropdown-item" href="{{ $editTabLink }}" id="{{ $editTabId }}" data-bs-toggle="tab"
                role="tab" aria-controls="{{ $editTabId }}">Edit</a>

        </div>
    </li>

</ul>
