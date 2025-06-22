<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    function create(int $idContact, AddressCreateRequest $request): JsonResponse {
        $user = Auth::user();
        $contact = Contact::query()->where("user_id", "=", $user->id)->where("id", "=", $idContact)->first();
        if (!isset($contact)){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    function get(int $idContact, int $idAddress) {
        $user = Auth::user();
        $contact = Contact::query()->where("user_id", "=", $user->id)->where("id", "=", $idContact)->first();
        if (!isset($contact)){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::query()->where("contact_id", $contact->id)->where("id", $idAddress)->first();
        if (!isset($address)){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new AddressResource($address);
    }
}
