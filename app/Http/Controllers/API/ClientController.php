<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AddClientRequest;
use App\Http\Requests\Client\EditClientRequest;
use App\Models\Client;
use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use apiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $client = Client::all();
            if (!$client->isEmpty()) {
                return $this->succesResponse($client, 'liste de tous les clients');
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'la liste des clients est vide'
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddClientRequest $request)
    {
        try {
            $client = new Client();
            $client->name = $request->name;
            $client->phone_number = $request->phone_number;
            $client->email = $request->email;
            $client->address = $request->address;
            $client->type = $request->type;
            $client->siret_siren = $request->siret_siren;
            $client->user_id = auth()->user()->id;
            if ($client->save()) {
                return $this->succesResponse($client, 'Client ajouté');
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $client = Client::with('User')->find($id);
            if (!$client) {
                return $this->errorResponse('Cet client n\'existe pas');
            } else {
                return $this->succesResponse($client, 'details clients');
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditClientRequest $request, int $id)
    {
        try {
            $client = Client::find($id);
            if (!$client) {
                return $this->errorResponse('Cet client n\'existe pas');
            } else {
                $client->name = $request->name;
                $client->phone_number = $request->phone_number;
                $client->email = $request->email;
                $client->address = $request->address;
                // particuliers ou bien entreprise
                $client->type = $request->type;
                $client->siret_siren = $request->siret_siren;
                $client->user_id = auth()->user()->id;
                if ($client->update()) {
                    return $this->succesResponse($client, 'Client ajouté');
                }
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $client = Client::find($id);
            if ($client) {
                $client->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'Client supprimé'
                ]);
            } else {
                return $this->errorResponse('Cet client n\'existe pas');
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function listClientTrainer(){
        try {
            $user = auth()->user();
            $client = $user->client;
            if (!$client->isEmpty()) {
                return $this->succesResponse($client, 'la liste des clients pour l\'utilisateur connexté');
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'La liste des clients pour l\'utilisateur conneté est vide'
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
