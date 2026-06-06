<?php

namespace App\Http\Controllers\Client;

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

        $request->session()->put('client_locale', $locale);

        if ($user) {
            $user->forceFill(['client_locale' => $locale])->save();
        }

        return redirect()->back()->withCookie(
            cookie()->forever('client_locale', $locale)
        );
    }
}
