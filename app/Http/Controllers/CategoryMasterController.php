<?php

namespace App\Http\Controllers;

use App\Models\CategoryMaster;
use App\Models\ClientMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryMasterController extends Controller
{
    public function index()
    {
        return view('categories.all');
    }

    public function allData()
    {
        $clientId = Auth::user()->client_id;

        $query = CategoryMaster::with('client')
            ->select(['category_id', 'category_name', 'client_id', 'status_id', 'created_at']);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        $categories = $query->get()
            ->map(fn($c) => [
                'category_id'   => $c->category_id,
                'category_name' => $c->category_name,
                'client_name'   => $c->client ? $c->client->client_name : 'Global',
                'status_id'     => $c->status_id,
                'created_at'    => $c->created_at,
            ]);

        return response()->json(['data' => $categories]);
    }

    public function create()
    {
        $clients = ClientMaster::orderBy('client_name')->get();
        return view('categories.add', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        $clientId = Auth::user()->client_id ?? ($request->client_id ?: null);

        CategoryMaster::create([
            'category_name' => $request->category_name,
            'client_id'     => $clientId,
            'status_id'     => $request->input('status_id', 1),
            'created_by'    => Auth::id(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category added successfully.');
    }

    public function edit($id)
    {
        $category = CategoryMaster::findOrFail($id);
        $clients  = ClientMaster::orderBy('client_name')->get();
        return view('categories.edit', compact('category', 'clients', 'id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        $category = CategoryMaster::findOrFail($id);
        $clientId = Auth::user()->client_id ?? ($request->client_id ?: null);

        $category->update([
            'category_name' => $request->category_name,
            'client_id'     => $clientId,
            'status_id'     => $request->input('status_id', 1),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        CategoryMaster::findOrFail($request->id)->update(['status_id' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        CategoryMaster::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
