<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CreateAdmin;
use App\Http\Requests\CreateManager;
use App\Models\Administration;
use App\Models\User;
use App\Models\Manager;

class AdministratorController extends Controller
{
    //

    public function create()
    {

    }

    public function store_information(CreateAdmin $request)
    {
        $data = [
            "web" => isset($request->web) ? $request->validated()['web'] : '',
            "name" => $request->validated()['name'],
            "receiving" => $request->validated()['receiving'],
            "society" => $request->validated()['society'],
            "nif_cif" => $request->validated()['nif_cif'],
            "province" => $request->validated()['province'],
            "city" => $request->validated()['city'],
            "postal_code" => $request->validated()['postal_code'],
            "address" => $request->validated()['address'],
            "email" => $request->validated()['email'],
            "phone" => $request->validated()['phone'],
            "account" => implode(' ', $request->account),
        ];
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('images'), $filename);
            $data["image"] = $filename;
        }

        $request->session()->put('administration', $data);

        return view('admins.add_manager');
    }

    public function store(CreateManager $request)
    {
        $data = [
            "name" => $request->validated()["name"],
            "last_name" => $request->validated()["last_name"],
            "last_name2" => $request->validated()["last_name2"],
            "nif_cif" => $request->validated()["nif_cif"],
            "birthday" => $request->validated()["birthday"],
            "email" => $request->validated()["email"],
            "phone" => $request->validated()["phone"],
            "comment" => $request->validated()["comment"],
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $file->move(public_path('manager'), $filename);
            $data["image"] = $filename;
        }

        $u = User::where('email',$request->validated()["email"])->first();
        if (!$u) {
            $u = new User;
            $u->name = $request->validated()["name"].' '.$request->validated()["last_name"];
            $u->email = $request->validated()["email"];
            $u->password = bcrypt(12345678);
            $u->save();
        }

        $data['user_id'] = $u->id;

        $administration = $request->session()->get("administration");
        
        $manager = Manager::create($data);

        $administration['manager_id'] = $manager->id;

        Administration::create($administration);

        $request->session()->forget('administration');

        return redirect('administrations');
    }
}
