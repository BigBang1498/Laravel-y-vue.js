<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentType\StoreDocumentTypeRequest;
use App\Http\Requests\DocumentType\UpdateDocumentTypeRequest;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentType::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', (bool)$request->input('status'));
        }

        $documentTypes = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('document_types.index', compact('documentTypes'));
    }

    public function create()
    {
        return view('document_types.create');
    }

    public function store(StoreDocumentTypeRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code'] = strtoupper($data['code']);

        $type = DocumentType::create($data);

        return redirect()->route('document-types.index')->with('success', "Tipo de documento '{$type->name}' registrado exitosamente.");
    }

    public function edit(DocumentType $documentType)
    {
        return view('document_types.edit', compact('documentType'));
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code'] = strtoupper($data['code']);

        $documentType->update($data);

        return redirect()->route('document-types.index')->with('success', "Tipo de documento '{$documentType->name}' actualizado correctamente.");
    }

    public function destroy(DocumentType $documentType)
    {
        $name = $documentType->name;
        $documentType->delete();

        return redirect()->route('document-types.index')->with('success', "El tipo de documento '{$name}' ha sido eliminado.");
    }

    public function toggleStatus(DocumentType $documentType)
    {
        $documentType->is_active = !$documentType->is_active;
        $documentType->save();

        $estado = $documentType->is_active ? 'activado' : 'desactivado';
        return redirect()->route('document-types.index')->with('success', "El tipo de documento '{$documentType->name}' ha sido {$estado}.");
    }
}
