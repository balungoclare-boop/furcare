@props([
    'announcement' => null,
    'action',
    'method' => 'POST',
])

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-3">
                <label for="is_pinned" class="inline-flex cursor-pointer items-center gap-3">
                    <input
                        id="is_pinned"
                        name="is_pinned"
                        type="checkbox"
                        value="1"
                        class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500"
                        @checked(old('is_pinned', $announcement?->is_pinned))
                    >
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">Pin this announcement</span>
                        <span class="block text-xs text-slate-500">Pinned announcements stay on top.</span>
                    </span>
                </label>
                <x-input-error :messages="$errors->get('is_pinned')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="priority" :value="__('Priority Level')" />
                <select
                    id="priority"
                    name="priority"
                    class="mt-1 block w-full rounded-md border-slate-300"
                >
                    <option value="normal" @selected(old('priority', $announcement?->priority ?? 'normal') === 'normal')>Normal</option>
                    <option value="important" @selected(old('priority', $announcement?->priority) === 'important')>Important</option>
                </select>
                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input
                id="title"
                name="title"
                class="mt-1 block w-full"
                :value="old('title', $announcement?->title)"
                placeholder="Free Anti-Rabies Vaccination"
                required
            />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <textarea
                id="description"
                name="description"
                rows="5"
                class="mt-1 block w-full rounded-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Free vaccination this Saturday from 9 AM to 3 PM"
                required
            >{{ old('description', $announcement?->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="publish_at" :value="__('Publish Date')" />
                <x-text-input
                    id="publish_at"
                    name="publish_at"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    :value="old('publish_at', optional($announcement?->publish_at)->format('Y-m-d\TH:i'))"
                />
                <x-input-error :messages="$errors->get('publish_at')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="expires_at" :value="__('Expiration Date')" />
                <x-text-input
                    id="expires_at"
                    name="expires_at"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    :value="old('expires_at', optional($announcement?->expires_at)->format('Y-m-d\TH:i'))"
                />
                <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="image" :value="__('Image Upload (Optional)')" />
            <input
                id="image"
                name="image"
                type="file"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700"
            >
            <x-input-error :messages="$errors->get('image')" class="mt-2" />

            @php
                $existingImage = $announcement?->image_path ? asset('storage/'.$announcement->image_path) : null;
            @endphp

            <img
                id="announcement-image-preview"
                src="{{ $existingImage }}"
                alt="Announcement image preview"
                class="mt-3 {{ $existingImage ? '' : 'hidden' }} max-h-52 w-full rounded-lg border border-slate-200 object-cover shadow"
            >
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save Announcement</x-ui.button>
        <x-ui.button :href="route('announcements.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>

<script>
    (() => {
        const fileInput = document.getElementById('image');
        const preview = document.getElementById('announcement-image-preview');

        if (!fileInput || !preview) return;

        fileInput.addEventListener('change', () => {
            const [file] = fileInput.files || [];
            if (!file) {
                if (!preview.getAttribute('src')) {
                    preview.classList.add('hidden');
                }
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target?.result ?? '';
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    })();
</script>
