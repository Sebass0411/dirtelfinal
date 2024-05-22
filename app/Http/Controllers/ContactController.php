<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $contacts = Auth::user()->contacts;
            return response()->json($contacts);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'CODERESPONSE' => 500,
                    'RESPONSE' => $e->getMessage(), 
                ]
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => 'nullable|string|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $contact = Auth::user()->contacts()->create($request->all()); 
            return response()->json($contact, 201);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'CODERESPONSE' => 500,
                    'RESPONSE' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        try {
            if ($contact->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return response()->json($contact);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'CODERESPONSE' => 500,
                    'RESPONSE' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        try {
            if ($contact->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'phone_number' => 'string|max:20',
                'email' => 'nullable|string|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $contact->update($request->all());
            return response()->json($contact, 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'CODERESPONSE' => 500,
                    'RESPONSE' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        try {
            if ($contact->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $contact->delete();
            return response()->json(['message' => 'Contacto eliminado correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'CODERESPONSE' => 500,
                    'RESPONSE' => $e->getMessage(),
                ]
            );
        }
    }
}
