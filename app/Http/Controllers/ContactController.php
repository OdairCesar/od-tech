<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::query()->create([
            ...$request->validated(),
            'source_url' => $request->headers->get('referer'),
        ]);

        return redirect()
            ->route('contact.show')
            ->with('status', 'Mensagem enviada com sucesso! Em breve entraremos em contato.');
    }
}
