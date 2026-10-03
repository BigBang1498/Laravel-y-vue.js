<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipient\StoreRecipientRequest;
use App\Http\Requests\Recipient\UpdateRecipientRequest;
use App\Models\Recipient;
use Illuminate\Http\Request;

class RecipientController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipient::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', (bool)$request->input('status'));
        }

        $recipients = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('recipients.index', compact('recipients'));
    }

    public function create()
    {
        return view('recipients.create');
    }

    public function store(StoreRecipientRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $recipient = Recipient::create($data);

        return redirect()->route('recipients.index')->with('success', "Destinatario '{$recipient->name}' registrado exitosamente.");
    }

    public function edit(Recipient $recipient)
    {
        return view('recipients.edit', compact('recipient'));
    }

    public function update(UpdateRecipientRequest $request, Recipient $recipient)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $recipient->update($data);

        return redirect()->route('recipients.index')->with('success', "Destinatario '{$recipient->name}' actualizado correctamente.");
    }

    public function destroy(Recipient $recipient)
    {
        $name = $recipient->name;
        $recipient->delete();

        return redirect()->route('recipients.index')->with('success', "El destinatario '{$name}' ha sido eliminado.");
    }
}
