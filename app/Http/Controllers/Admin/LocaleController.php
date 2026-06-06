<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, ['ms', 'en'], true)) {
            abort(404);
        }

        $user = $request->user();

        if ($user) {
            $user->forceFill(['admin_locale' => $locale])->save();
        }

        return redirect()->back();
    }
}
