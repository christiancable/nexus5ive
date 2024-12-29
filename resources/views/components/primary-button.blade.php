<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary small d-inline-flex align-items-center']) }}>
    {{ $slot }}
</button>
