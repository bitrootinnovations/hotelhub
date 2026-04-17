<?php

namespace App\Http\Controllers;

use App\Models\MenuMaster;
use App\Models\CategoryMaster;
use App\Models\ClientMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuMasterController extends Controller
{
    public function index()
    {
        return view('menus.all');
    }

    public function allData()
    {
        $clientId = Auth::user()->client_id;

        $query = MenuMaster::with(['category', 'client'])
            ->select(['menu_id', 'menu_name', 'category_id', 'food_type', 'price', 'gst_percentage', 'stock_type', 'quantity', 'image', 'client_id', 'status_id', 'created_at']);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        $menus = $query->get()
            ->map(fn($m) => [
                'menu_id'        => $m->menu_id,
                'menu_name'      => $m->menu_name,
                'category_name'  => $m->category ? $m->category->category_name : '-',
                'food_type'      => $m->food_type,
                'food_type_label'=> $m->food_type == 1 ? 'Veg' : 'Non-Veg',
                'price'          => number_format($m->price, 2),
                'gst_percentage' => $m->gst_percentage,
                'stock_type'     => $m->stock_type,
                'quantity'       => $m->quantity ?? '-',
                'image'          => $m->image,
                'client_name'    => $m->client ? $m->client->client_name : '-',
                'status_id'      => $m->status_id,
                'created_at'     => $m->created_at,
            ]);

        return response()->json(['data' => $menus]);
    }

    public function create()
    {
        $categories = CategoryMaster::where('status_id', 1)->get();
        $clients    = ClientMaster::orderBy('client_name')->get();
        return view('menus.add', compact('categories', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_name'      => 'required|string|max:150',
            'category_id'    => 'required|exists:category_masters,category_id',
            'food_type'      => 'required|in:1,2',
            'price'          => 'required|numeric|min:0',
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'stock_type'     => 'required|in:Unit,Kg',
            'client_id'      => 'required|exists:client_masters,client_id',
        ]);

        $data = $request->only(['menu_name', 'category_id', 'food_type', 'price', 'gst_percentage', 'stock_type', 'quantity', 'client_id', 'status_id']);
        $data['status_id']  = $request->input('status_id', 1);
        $data['created_by'] = Auth::id();
        if (Auth::user()->client_id) {
            $data['client_id'] = Auth::user()->client_id;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        MenuMaster::create($data);

        return redirect()->route('menus.index')->with('success', 'Menu item added successfully.');
    }

    public function edit($id)
    {
        $menu       = MenuMaster::findOrFail($id);
        $categories = CategoryMaster::where('status_id', 1)->get();
        $clients    = ClientMaster::orderBy('client_name')->get();
        return view('menus.edit', compact('menu', 'categories', 'clients', 'id'));
    }

    public function update(Request $request, $id)
    {
        $menu = MenuMaster::findOrFail($id);

        $request->validate([
            'menu_name'      => 'required|string|max:150',
            'category_id'    => 'required|exists:category_masters,category_id',
            'food_type'      => 'required|in:1,2',
            'price'          => 'required|numeric|min:0',
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'stock_type'     => 'required|in:Unit,Kg',
            'client_id'      => 'required|exists:client_masters,client_id',
        ]);

        $data              = $request->only(['menu_name', 'category_id', 'food_type', 'price', 'gst_percentage', 'stock_type', 'quantity', 'client_id', 'status_id']);
        $data['status_id'] = $request->input('status_id', 1);
        if (Auth::user()->client_id) {
            $data['client_id'] = Auth::user()->client_id;
        }

        if ($request->hasFile('image')) {
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('menus.index')->with('success', 'Menu item updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        MenuMaster::findOrFail($request->id)->update(['status_id' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $menu = MenuMaster::findOrFail($id);
        if ($menu->image) Storage::disk('public')->delete($menu->image);
        $menu->delete();
        return response()->json(['success' => true]);
    }
}
