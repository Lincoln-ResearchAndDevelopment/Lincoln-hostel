<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostelRule;
use App\Models\Hostel;
use Illuminate\Http\Request;

class HostelRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $rules = HostelRule::orderBy('category')->orderBy('order')->paginate(20);
        $categories = HostelRule::getCategories();
        return view('admin.rules.index', compact('rules', 'categories'));
    }

    public function create()
    {
        $categories = HostelRule::getCategories();
        $hostels = Hostel::where('status', 'active')->get();
        return view('admin.rules.create', compact('categories', 'hostels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'hostel_id' => 'nullable|exists:hostels,id',
            'order' => 'nullable|integer|min:0',
        ]);

        HostelRule::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'hostel_id' => $request->hostel_id,
            'order' => $request->order ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.rules.index')->with('success', 'Hostel rule created successfully.');
    }

    public function edit(HostelRule $rule)
    {
        $categories = HostelRule::getCategories();
        $hostels = Hostel::where('status', 'active')->get();
        return view('admin.rules.edit', compact('rule', 'categories', 'hostels'));
    }

    public function update(Request $request, HostelRule $rule)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'hostel_id' => 'nullable|exists:hostels,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $rule->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'hostel_id' => $request->hostel_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.rules.index')->with('success', 'Hostel rule updated successfully.');
    }

    public function destroy(HostelRule $rule)
    {
        $rule->delete();
        return redirect()->route('admin.rules.index')->with('success', 'Hostel rule deleted successfully.');
    }

    public function toggleStatus(HostelRule $rule)
    {
        $rule->update(['is_active' => !$rule->is_active]);
        return redirect()->back()->with('success', 'Rule status updated.');
    }
}
