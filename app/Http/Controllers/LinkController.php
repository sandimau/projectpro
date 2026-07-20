<?php

namespace App\Http\Controllers;

use App\Models\LinkPage;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * Display the public link page
     */
    public function show($slug)
    {
        $linkPage = LinkPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $socialLinks = $linkPage->socialLinks;
        $sections = $linkPage->getItemsBySection();

        return view('links.show', compact('linkPage', 'socialLinks', 'sections'));
    }
}

