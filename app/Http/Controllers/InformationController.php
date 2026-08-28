<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $informations = Information::latest()->get();

        return view('admin.dashboard', compact('informations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'main_topic' => 'required',
            'description' => 'required',
        ]);

        Information::create([
            'main_topic' => $request->main_topic,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('status', 'Information added successfully!');
    }

    public function update(Request $request, Information $information)
    {
        $request->validate([
            'main_topic' => 'required',
            'description' => 'required',
        ]);

        $information->update([
            'main_topic' => $request->main_topic,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('status', 'Information updated successfully!');
    }

    public function destroy(Information $information)
    {
        $information->delete();

        return redirect()->route('admin.dashboard')
            ->with('status', 'Information deleted successfully!');
    }
}