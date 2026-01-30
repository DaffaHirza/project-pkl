<!-- Assign Users Modal -->
<dialog id="assignModal{{ $card->id }}" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full max-w-sm p-0">
        <!-- Close Button -->
        <button onclick="document.getElementById('assignModal{{ $card->id }}').close()" 
                class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="px-6 py-6">
            <!-- Header -->
            <div class="mb-6 pb-4 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-900">Atur Penugasan</h3>
            </div>

            <!-- Form -->
            <form action="{{ route('cards.assign', $card) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Pilih Pengguna</label>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach(\App\Models\User::all() as $user)
                            <div x-data="{ checkboxToggle: {{ $card->assignedUsers->contains('id', $user->id) ? 'true' : 'false' }} }">
                                <label class="flex cursor-pointer items-center text-sm font-medium text-gray-700 dark:text-gray-400 select-none gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 transition">
                                    <div class="relative">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="sr-only"
                                               @change="checkboxToggle = !checkboxToggle"
                                               @if($card->assignedUsers->contains('id', $user->id)) checked @endif />
                                        <div :class="checkboxToggle ? 'border-indigo-500 bg-indigo-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                                            class="hover:border-indigo-500 dark:hover:border-indigo-500 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                                            <span :class="checkboxToggle ? '' : 'opacity-0'">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white/90">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('assignModal{{ $card->id }}').close()" 
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </button>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-sm font-medium text-white sm:w-auto">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</dialog>
