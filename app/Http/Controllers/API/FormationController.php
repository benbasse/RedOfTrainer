<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Formation\AddFormationRequest;
use App\Http\Requests\Formation\EditFormationRequest;
use App\Models\Formation;
use App\Models\User;
use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    use apiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $formation = Formation::with('user')->get();
            if ($formation->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'la liste des formations est vide'
                ]);
            } else {
                return $this->succesResponse($formation, "La liste des formations");
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddFormationRequest $request)
    {
        try {
            $formation = new Formation();
            $formation->title = $request->title;
            $formation->description = $request->description;
            $formation->user_id = auth()->user()->id;
            if ($formation->save()) {
                return $this->succesResponse($formation, 'Formation enregistrée');
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
            $formation = Formation::find($id);
            if (!$formation) {
                return $this->errorResponse('Cette formation n\'existe pas', 404);
            } else {
                return $this->succesResponse($formation, 'details de la formation');
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditFormationRequest $request, int $id)
    {
        try {
            $formation = Formation::find($id);
            if (!$formation) {
                return $this->errorResponse('Formation non trouvée', 404);
            } else {
                $formation->title = $request->title;
                $formation->description = $request->description;
                $formation->user_id = auth()->user()->id;
                if ($formation->update()) {
                    return $this->succesResponse($formation, 'formation modifiée');
                }
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $formation = Formation::find($id);
            if ($formation) {
                $formation->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'formation supprimée'
                ]);
            } else {
                return $this->errorResponse('formation non trouvée');
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * List Formations for one Trainer
     */
    public function listFormationTrainer(){
        try {
            $user = auth()->user();
            $formation = $user->formation;
            if (!$formation->isEmpty()) {
                return $this->succesResponse($formation, 'la liste des formations pour l\'utilisateur connecté');
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'la liste des formations pour cet utilisateur est vide'
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }

    }
}
