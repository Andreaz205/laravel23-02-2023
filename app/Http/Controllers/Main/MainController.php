<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\BannerImages;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:contact list', ['only' => ['index', 'show']]);
        $this->middleware('can:contact create', ['only' => ['create', 'store']]);
        $this->middleware('can:contact edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:contact delete', ['only' => ['destroy']]);

        $this->middleware('can:banner list', ['only' => ['index', 'show']]);
        $this->middleware('can:banner create', ['only' => ['create', 'store']]);
        $this->middleware('can:banner edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:banner delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $contacts = Contact::first();
        $bannerItems = BannerImages::orderBy('position', 'ASC')->get();
        return inertia('Home/Index', [
            'contacts' => $contacts,
            'bannerItems' => $bannerItems,
            'can-contacts' => [
                'edit' => Auth('admin')->user()?->can('contacts edit'),
                'delete' => Auth('admin')->user()?->can('contacts delete'),
            ],
            'can-banner' => [
                'list' => Auth('admin')->user()->can('banner list'),
                'create' => Auth('admin')->user()->can('banner create'),
                'edit' => Auth('admin')->user()->can('banner edit'),
                'delete' => Auth('admin')->user()->can('banner delete'),
            ]
        ]);
    }
}
