<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance_sheet;
use App\Models\Session;
use App\Traits\apiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    use apiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $session = Session::all();
            if (!$session->isEmpty()) {
                return $this->succesResponse($session, 'Liste des sessions');
            } else {
                return response()->json([
                    "status" => 200,
                    "message" => "la liste des session est vide"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $session = new Session();
            $session->name = $request->name;
            $session->date = $request->date;
            $session->address = $request->address;
            $session->duration = $request->duration;
            $session->number_of_trainees = $request->number_of_trainees;
            $session->human_verification = $request->human_verification;
            $session->trainer_verification_notes = $request->trainer_verification_notes;
            $session->total_hours = $session->duration * $session->number_of_trainees;
            $session->attendance_sheet_id = $request->attendance_sheet_id;
            $session->nmanual_validation = $request->manual_validation;
            $session->user_id = auth()->user()->id;
            $session->save();
            return $this->succesResponse($session, 'Session ajoutée');
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * save file path on the db
     */
    public function storeFileDatabase(Request $request){
        $attendance_sheet = new Attendance_sheet();
        $attendance_sheet->path = $this->storeFile($request->attendance_sheet);
        if ($attendance_sheet->save()) {
            return $this->succesResponse($attendance_sheet, 'upload reussi');
        }
    }

    /**
     * upload file paht
     */
    private function storeFile($file)
    {
        return $file->store('session', 'public');
    }
    

    /**
     * This function provide by AI chatGPT 
     */
    public function AIReadFile($content){

    }
    public function show(int $id)
    {
        try {
            $session = Session::find($id);
            if(!$session){
                return $this->errorResponse("cette session n'existe pas");
            } else {
                return $this->succesResponse($session, 'Details de cette session');
            }
        } catch (Exception $e){
            return response()->json($e);
        }
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        try {
            $session = Session::find($id);
            if (!$session) {
                return $this->errorResponse("cette session n'existe pas");
            } else {
                $session->delete();
                return response()->json([
                    "status" => 200,
                    "message" => "session supprimée"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function listSessionTrainer()
    {
        try {
            $user = auth()->user();
            $session = $user->session;
            if (!$session->isEmpty()) {
                return $this->succesResponse($session, 'la liste des sessions pour l\'utilisateur connecté');
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'la liste des sessions pour cet utilisateur est vide'
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }

    }
}
