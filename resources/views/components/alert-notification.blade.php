@props(['title' => null, 'items' => [], 'type' => 'error'])

<div id="alertContainer" class="fixed top-20 right-4 z-50">
    @if ($title && count($items) > 0)
        <div class="animate-slide-in-left mb-3">
            <div class="{{ $type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-yellow-50 border-yellow-200 text-yellow-800' }} border text-sm rounded-lg p-4 shadow-lg max-w-md"
                role="alert" tabindex="-1">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <path d="m15 9-6 6" />
                            <path d="m9 9 6 6" />
                        </svg>
                    </div>
                    <div class="ms-4 flex-1">
                        <h3 class="text-sm font-semibold">
                            {{ $title }}
                        </h3>
                        <div class="mt-2 text-sm">
                            <ul class="list-disc space-y-1 ps-5">
                                @foreach ($items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // HELPER: Show custom alert notification
    function showAlert(title, items, type = 'warning') {
        console.log('showAlert called:', {
            title,
            items,
            type
        });

        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            console.error('Alert container tidak ditemukan!');
            return;
        }

        const bgColor = type === 'error' ? 'bg-red-50' : 'bg-yellow-50';
        const borderColor = type === 'error' ? 'border-red-200' : 'border-yellow-200';
        const textColor = type === 'error' ? 'text-red-800' : 'text-yellow-800';

        const alertHTML = `
            <div class="animate-slide-in-left mb-3">
                <div class="${bgColor} border ${borderColor} text-sm ${textColor} rounded-lg p-4 shadow-lg max-w-md" role="alert" tabindex="-1">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <path d="m15 9-6 6" />
                                <path d="m9 9 6 6" />
                            </svg>
                        </div>
                        <div class="ms-4 flex-1">
                            <h3 class="text-sm font-semibold">
                                ${title}
                            </h3>
                            <div class="mt-2 text-sm ${textColor}">
                                <ul class="list-disc space-y-1 ps-5">
                                    ${items.map(item => `<li>${item}</li>`).join('')}
                                </ul>
                            </div>
                        </div>
                        <button onclick="this.closest('[id^=alert-]')?.remove()" class="ms-3 shrink-0 ${textColor} hover:opacity-70 transition-opacity">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        const alertDiv = document.createElement('div');
        alertDiv.id = 'alert-' + Date.now();
        alertDiv.innerHTML = alertHTML;

        alertContainer.appendChild(alertDiv);
        console.log('Alert ditambahkan ke DOM');

        // Auto remove after 6 seconds
        setTimeout(() => {
            alertDiv.style.animation = 'slide-out-left 0.3s ease-out';
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slide-in-left {
            from {
                opacity: 0;
                transform: translateX(-100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slide-out-left {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-100%);
            }
        }
        
        .animate-slide-in-left {
            animation: slide-in-left 0.3s ease-out;
        }
    `;
    document.head.appendChild(style);
</script>
