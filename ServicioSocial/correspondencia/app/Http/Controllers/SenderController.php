<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sender\StoreSenderRequest;
use App\Http\Requests\Sender\UpdateSenderRequest;
use App\Models\Sender;
use Illuminate\Http\Request;

class SenderController extends Controller
{
    public function index(Request $request)
    {
        $query = Sender::query();

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

        $senders = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('senders.index', compact('senders'));
    }

    public function create()
    {
        return view('senders.create');
    }

    public function store(StoreSenderRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $sender = Sender::create($data);

        return redirect()->route('senders.index')->with('success', "Remitente '{$sender->name}' registrado exitosamente.");
    }

    public function edit(Sender $sender)
    {
        return view('senders.edit', compact('sender'));
    }

    public function update(UpdateSenderRequest $request, Sender $sender)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $sender->update($data);

        return redirect()->route('senders.index')->with('success', "Remitente '{$sender->name}' actualizado correctamente.");
    }

    public function destroy(Sender $sender)
    {
        $name = $sender->name;
        $sender->delete();

        return redirect()->route('senders.index')->with('success', "El remitente '{$name}' ha sido eliminado.");
    }
}
