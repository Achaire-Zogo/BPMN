<?php

namespace App\Http\Controllers;

use App\Models\BPMNDiagram;
use Illuminate\Http\Request;

class BPMNController extends Controller
{
    public function index()
    {
        $diagrams = BPMNDiagram::orderBy('updated_at', 'desc')->get();
        return view('bpmn.index', compact('diagrams'));
    }

    public function modeler($id = null)
    {
        $diagram = $id ? BPMNDiagram::findOrFail($id) : null;
        return view('bpmn.modeler', compact('diagram'));
    }

    public function saveDiagram(Request $request)
    {
        $request->validate([
            'xml' => 'required|string',
            'name' => 'required|string|max:255'
        ]);

        $diagram = BPMNDiagram::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'xml_content' => $request->xml
            ]
        );

        return response()->json([
            'success' => true,
            'diagram' => $diagram
        ]);
    }

    public function deleteDiagram($id)
    {
        $diagram = BPMNDiagram::findOrFail($id);
        $diagram->delete();

        return response()->json(['success' => true]);
    }
}
