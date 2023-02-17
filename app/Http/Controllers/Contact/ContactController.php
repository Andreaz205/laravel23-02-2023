<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\CreateEditRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:contact edit', ['only' => 'storeContacts']);
        $this->middleware('can:contact delete', ['only' => 'clearContacts']);
    }

    public function storeContacts(CreateEditRequest $request)
    {
        $data = $request->validated();
        $contacts = Contact::first();
        if (!($data['phone'] > 9999999999 && $data['phone'] < 99999999999)) {
            return view('home', compact('contacts'))->withErrors(['phone' => 'Неправильный номер']);
        }

        if (isset($contacts)) {
            $contacts->update($data);
        } else {
            $contacts = Contact::create($data);
        }
        return $contacts;
    }

    public function clearContacts()
    {
        Contact::all()->delete();
        return response()->json(['status' => 'success']);
    }
}
