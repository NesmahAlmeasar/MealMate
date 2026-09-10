<button {{ $attributes->merge(['type' => 'submit', 'class' => 'action-btn delete', 'style' => 'width: auto; padding: var(--spacing-sm) var(--spacing-lg); border-radius: var(--radius-md); font-weight: 600;']) }}>
    {{ $slot }}
</button>
