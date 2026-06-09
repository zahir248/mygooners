@php
    $adminAuthFlashToasts = [];

    if (session('status')) {
        $adminAuthFlashToasts[] = ['type' => 'success', 'message' => session('status')];
    }

    if (session('success')) {
        $adminAuthFlashToasts[] = ['type' => 'success', 'message' => session('success')];
    }

    if (session('error')) {
        $adminAuthFlashToasts[] = ['type' => 'error', 'message' => session('error')];
    }

    if (session('warning')) {
        $adminAuthFlashToasts[] = ['type' => 'warning', 'message' => session('warning')];
    }

    if ($errors->any()) {
        $errorHeading = $authFlashErrorHeading ?? __('Aduh! Ada yang tidak kena.');
        $adminAuthFlashToasts[] = [
            'type' => 'error',
            'message' => $errorHeading . "\n" . implode("\n", $errors->all()),
        ];
    }
@endphp

@if(!empty($adminAuthFlashToasts))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashes = @json($adminAuthFlashToasts);
        flashes.forEach(function (flash) {
            showAdminNotification(flash.message, flash.type);
        });
    });
</script>
@endif
