<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{



    public function index()
    {
        $contacts = Contact::first();
        return inertia('Home/Index', [
            'contacts' => $contacts,
            'can-contacts' => [
                'edit' => Auth('admin')->user()?->can('contacts edit'),
                'delete' => Auth('admin')->user()?->can('contacts delete'),
            ]
        ]);
    }
}
