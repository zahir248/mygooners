@php
    $adminFlashToasts = [];

    if (session('success')) {
        $adminFlashToasts[] = ['type' => 'success', 'message' => session('success')];
    }

    if (session('error')) {
        $adminFlashToasts[] = ['type' => 'error', 'message' => session('error')];
    }

    if (session('warning')) {
        $adminFlashToasts[] = ['type' => 'warning', 'message' => session('warning')];
    }

    if (session('info')) {
        $adminFlashToasts[] = ['type' => 'info', 'message' => session('info')];
    }

    if (session('status')) {
        $adminFlashToasts[] = ['type' => 'success', 'message' => session('status')];
    }

    if ($errors->any()) {
        $adminFlashToasts[] = [
            'type' => 'error',
            'message' => __('Aduh! Ada yang tidak kena.') . "\n" . implode("\n", $errors->all()),
        ];
    }
@endphp

@if(!empty($adminFlashToasts))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashes = @json($adminFlashToasts);
        flashes.forEach(function (flash) {
            showAdminNotification(flash.message, flash.type);
        });
    });
</script>
@endif
