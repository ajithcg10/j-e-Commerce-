<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\RolesEnum;
use App\VedorStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendoerController extends Controller
{
    public function profile(Vendor $vendor)
    {
        return view('vendor.profile');
    }
    public function store(Request $request)
    {
     $request->validate([
    'store_name' => ['required', 'regex:/^[a-z0-9-]+$/', Rule::unique('vendors', 'store_name')->ignore($request->user()->vendor?->id)],
    'store_adress' => 'nullable',
], [
    'store_name.regex' => 'Store Name must only contain lowercase alphanumeric characters and dashes.',
]



);

    $user = $request->user();
    $vendor  = $user->vendor ?: new Vendor();
    $vendor->user_id = $user->id;
    $vendor->status = VedorStatusEnum::Approved->value;
    $vendor->store_name = $request->store_name;
    $vendor->store_adress = $request->store_adress;
    $vendor->save();
    $user->assignRole(RolesEnum::Vendor);
    }
}
