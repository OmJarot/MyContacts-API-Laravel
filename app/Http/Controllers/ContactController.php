<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
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

    function update(int $id, ContactUpdateRequest $request): ContactResource {
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

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();
        return new ContactResource($contact);
    }

    function delete(int $id):JsonResponse {
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
        $contact->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    function search(Request $request): ContactCollection {
        $user = Auth::user();
        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $contacts = Contact::query()->where("user_id", $user->id);
        $contacts->where(function (Builder $builder) use ($request){
            $name = $request->input("name");
            if ($name){
                $builder->where(function (Builder $builder) use ($name){
                    $builder->orWhere("first_name", "like", "%".$name."%");
                    $builder->orWhere("last_name", "like", "%".$name."%");
                });
            }

            $email = $request->input("email");
            if ($email){
                $builder->where("email", "like", "%".$email."%");
            }

            $phone = $request->input("phone");
            if ($phone){
                $builder->where("phone", "like", "%".$phone."%");
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
