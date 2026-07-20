<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinkPage;
use App\Models\LinkItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkPageController extends Controller
{
    /**
     * Display a listing of link pages
     */
    public function index()
    {
        $linkPages = LinkPage::withCount('items')->latest()->get();
        return view('admin.links.index', compact('linkPages'));
    }

    /**
     * Show the form for creating a new link page
     */
    public function create()
    {
        return view('admin.links.create');
    }

    /**
     * Store a newly created link page
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:link_pages,slug',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'background_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'button_color' => 'nullable|string|max:7',
            'button_text_color' => 'nullable|string|max:7',
        ]);

        $data = $request->except('logo');
        $data['slug'] = Str::slug($request->slug);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $uploadPath = public_path('uploads/links');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $logo->move($uploadPath, $logoName);
            $data['logo'] = $logoName;
        }

        LinkPage::create($data);

        return redirect()->route('linkPages.index')
            ->with('success', 'Link Page berhasil dibuat!');
    }

    /**
     * Display the specified link page
     */
    public function show(LinkPage $linkPage)
    {
        $linkPage->load(['socialLinks', 'linkItems']);
        $sections = $linkPage->getItemsBySection();
        return view('admin.links.show', compact('linkPage', 'sections'));
    }

    /**
     * Show the form for editing the specified link page
     */
    public function edit(LinkPage $linkPage)
    {
        return view('admin.links.edit', compact('linkPage'));
    }

    /**
     * Update the specified link page
     */
    public function update(Request $request, LinkPage $linkPage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:link_pages,slug,' . $linkPage->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'background_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'button_color' => 'nullable|string|max:7',
            'button_text_color' => 'nullable|string|max:7',
        ]);

        $data = $request->except('logo');
        $data['slug'] = Str::slug($request->slug);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($linkPage->logo && file_exists(public_path('uploads/links/' . $linkPage->logo))) {
                unlink(public_path('uploads/links/' . $linkPage->logo));
            }
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $uploadPath = public_path('uploads/links');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $logo->move($uploadPath, $logoName);
            $data['logo'] = $logoName;
        }

        $linkPage->update($data);

        return redirect()->route('linkPages.index')
            ->with('success', 'Link Page berhasil diupdate!');
    }

    /**
     * Remove the specified link page
     */
    public function destroy(LinkPage $linkPage)
    {
        // Delete logo if exists
        if ($linkPage->logo && file_exists(public_path('uploads/links/' . $linkPage->logo))) {
            unlink(public_path('uploads/links/' . $linkPage->logo));
        }

        $linkPage->delete();

        return redirect()->route('linkPages.index')
            ->with('success', 'Link Page berhasil dihapus!');
    }

    /**
     * Manage link items for a page
     */
    public function items(LinkPage $linkPage)
    {
        $items = $linkPage->items()->get();
        return view('admin.links.items.index', compact('linkPage', 'items'));
    }

    /**
     * Create new link item
     */
    public function createItem(LinkPage $linkPage)
    {
        return view('admin.links.items.create', compact('linkPage'));
    }

    /**
     * Store link item
     */
    public function storeItem(Request $request, LinkPage $linkPage)
    {
        $request->validate([
            'type' => 'required|in:social,link',
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'section' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $data = $request->except('icon');
        $data['link_page_id'] = $linkPage->id;

        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconName = time() . '_' . $icon->getClientOriginalName();
            $uploadPath = public_path('uploads/links/icons');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $icon->move($uploadPath, $iconName);
            $data['icon'] = $iconName;
        }

        LinkItem::create($data);

        return redirect()->route('linkPages.items', $linkPage)
            ->with('success', 'Link Item berhasil ditambahkan!');
    }

    /**
     * Edit link item
     */
    public function editItem(LinkPage $linkPage, LinkItem $item)
    {
        return view('admin.links.items.edit', compact('linkPage', 'item'));
    }

    /**
     * Update link item
     */
    public function updateItem(Request $request, LinkPage $linkPage, LinkItem $item)
    {
        $request->validate([
            'type' => 'required|in:social,link',
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'section' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $data = $request->except('icon');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($item->icon && file_exists(public_path('uploads/links/icons/' . $item->icon))) {
                unlink(public_path('uploads/links/icons/' . $item->icon));
            }
            $icon = $request->file('icon');
            $iconName = time() . '_' . $icon->getClientOriginalName();
            $uploadPath = public_path('uploads/links/icons');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $icon->move($uploadPath, $iconName);
            $data['icon'] = $iconName;
        }

        $item->update($data);

        return redirect()->route('linkPages.items', $linkPage)
            ->with('success', 'Link Item berhasil diupdate!');
    }

    /**
     * Delete link item
     */
    public function destroyItem(LinkPage $linkPage, LinkItem $item)
    {
        // Delete icon if exists
        if ($item->icon && file_exists(public_path('uploads/links/icons/' . $item->icon))) {
            unlink(public_path('uploads/links/icons/' . $item->icon));
        }

        $item->delete();

        return redirect()->route('linkPages.items', $linkPage)
            ->with('success', 'Link Item berhasil dihapus!');
    }
}

