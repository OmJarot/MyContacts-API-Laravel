<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    function create(ContactCreateRequest $request): JsonResponse{
        $user = Auth::user();
        $data = $request->validated();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    function get(int $id): ContactResource {
        $user = Auth::user();

        $contact = Contact::query()->where("id", "=" ,$id)->where("user_id", "=", $user->id)->first();

        if (!isset($contact)){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new ContactResource($contact);
    }
}
