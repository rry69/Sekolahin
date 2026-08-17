<div class="space-y-6">
    <x-help-steps title="Mengelola akun" icon="fa-gear" :steps="[
        'Ubah <strong>nama &amp; email</strong> di kartu pertama.',
        'Ganti <strong>password</strong> di kartu kedua — butuh password lama.',
        'Hapus akun hanya jika benar-benar ingin keluar — permanen.',
    ]" />
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
