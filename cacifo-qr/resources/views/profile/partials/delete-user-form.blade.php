<section class="space-y-6">
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        <span class="delete-btn-text">{{ app()->getLocale() === 'en' ? 'Delete Account' : 'Eliminar Conta' }}</span>
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ app()->getLocale() === 'en' ? 'Are you sure you want to delete your account?' : 'Tem a certeza que pretende eliminar a conta?' }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ app()->getLocale() === 'en' ? 'Once your account is deleted, all data will be permanently removed. Please enter your password to confirm.' : 'Uma vez que a sua conta é apagada, todos os dados são permanentemente apagados. Por favor, introduza a sua palavra-passe para confirmar.' }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ app()->getLocale() === 'en' ? 'Password' : 'Palavra-passe' }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ app()->getLocale() === 'en' ? 'Password' : 'Palavra-passe' }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ app()->getLocale() === 'en' ? 'Cancel' : 'Cancelar' }}
                </x-secondary-button>
                <x-danger-button class="ms-3">
                    {{ app()->getLocale() === 'en' ? 'Delete Account' : 'Eliminar Conta' }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
