<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddUserRequest;
use App\Http\Requests\User\EditPasswordUserRequest;
use App\Http\Requests\User\EditUserRequest;
use App\Models\User;
use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class TrainerController extends Controller
{
    use apiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = User::with('Formation')->with('Client')->get();
            if ($user->isEmpty()) {
                return response()->json([
                    "status" => 200,
                    "message" => "La liste des trainers est vide"
                ]);
            } else {
                return $this->succesResponse($user, 'liste des trainers');
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
            $user = User::with('Client')->with('Formation')->find($id);
            if (!$user) {
                return $this->errorResponse('Utilisateur non trouvé');
            } else {
                return $this->succesResponse($user, 'details de cet utilisateur');
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
            $user = User::find($id);
            if (!$user) {
                return $this->errorResponse("Cet utilisateur n'existe pas");
            } else {
                $user->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'Trainer supprimé'
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Register a new Trainer
     */
    public function register(AddUserRequest $request){
        try {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;
            $user->ip_address = $request->ip();
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                return $this->succesResponse($user, 'Inscription réussi');
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Update Trainer Account
     */
    public function updateTrainer(EditUserRequest $request, $id){
        try {
            $user = User::find($id);
            if (auth()->user()->id !== $user->id) {
                return $this->errorResponse("Vous n'avez pas la permission de modifier cet utilisateur", 403);
            } else {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->phone_number = $request->phone_number;
                $user->address = $request->address;
                $user->ip_address = $request->ip();
                $user->email = $request->email;
                if ($user->save()) {
                    return $this->succesResponse($user, 'Compte modifié');
                }
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function editPassword(EditPasswordUserRequest $request){
        try {
            $user = auth()->user();
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse("l'ancien mot de passe est incorrect");
            } else {
                $user->password = Hash::make($request->new_password);
                if ($user->save()) {
                    return $this->succesResponse($user, 'mot de passe modifié');
                }
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


}
