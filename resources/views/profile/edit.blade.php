<x-layouts.app title="Profile">
    <x-ui.page-header title="Profile" description="Manage your account settings.">
        <x-slot:breadcrumb>
            <x-ui.breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Profile']]" />
        </x-slot:breadcrumb>
    </x-ui.page-header>

    <form method="POST" action="{{ route('profile.update') }}" class="mx-auto max-w-2xl">
        @csrf
        @method('PUT')
        <x-ui.card>
            <div class="mb-6 flex items-center gap-4">
                <x-ui.avatar size="lg">{{ $user->name }}</x-ui.avatar>
                <div>
                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->isAdmin() ? 'Administrator' : 'Staff Member' }}</p>
                </div>
            </div>
            <div class="grid gap-6">
                <x-ui.input label="Name" name="name" :value="old('name', $user->name)" required />
                <x-ui.input label="Email" name="email" type="email" :value="old('email', $user->email)" required />
                <x-ui.input label="New Password" name="password" type="password" hint="Leave blank to keep current password" />
                <x-ui.input label="Confirm Password" name="password_confirmation" type="password" />
            </div>
        </x-ui.card>
        <div class="mt-6 flex justify-end">
            <x-ui.button type="submit">Save Profile</x-ui.button>
        </div>
    </form>
</x-layouts.app>
