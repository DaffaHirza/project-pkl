<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-brand-500 border border-transparent rounded-lg font-medium text-sm text-white tracking-wide hover:bg-brand-600 focus:bg-brand-600 active:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-gray-dark transition ease-in-out duration-200 shadow-sm shadow-brand-500/30 hover:shadow-md hover:shadow-brand-500/40']) }}>
    {{ $slot }}
</button>
