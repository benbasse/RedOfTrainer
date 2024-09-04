<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Devis\AddDevisRequest;
use App\Http\Requests\Devis\EditDevisRequest;
use App\Mail\DevisMail;
use App\Models\Client;
use App\Models\Devis;
use App\Models\Devis_line_items;
use App\Models\line_items_devis;
use App\Traits\apiResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DevisController extends Controller
{
    use apiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $devis = Devis::with('Devis_Line_items')->get();
            if (!$devis->isEmpty()) {
                return $this->succesResponse($devis, 'Liste des devis');
            } else {
                return response()->json([
                    "status" => 201,
                    "message" => "La liste des devis est vide"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AddDevisRequest $request)
    {
        try {
            $devis = new Devis();
            $devis->devis_number = $this->generateUniqueIdentifier();
            $client = Client::find($request->client_id);
            if (!$client) {
                return $this->errorResponse('Le client sélectionné n\'existe pas', 400);
            }
            $devis->client_id = $client->id;
            $devis->user_id = auth()->user()->id;
            // $devis->status = $request->status;
            $devis->due_date = $request->due_date;
            $devis->special_conditions = $request->special_conditions;
            $devis->internal_notes = $request->internal_notes;
            $devis->sent_to = $client->email;

            // Initialisation des montants totaux
            $devis->total_amount_ht = 0;
            $devis->total_vat = 0;
            $devis->total_amount_ttc = 0;

            // Sauvegarde initiale de la devis pour obtenir l'ID
            $devis->save();

            // Boucle pour récupérer tous les line_items
            foreach ($request->line_items_devis as $item) {
                // Création du line_item
                $line_item = new Devis_line_items();
                $line_item->title = $item['title'];
                $line_item->date = $item['date'];
                $line_item->discount = $item['discount'];
                $line_item->description = $item['description'];
                $line_item->unit_price_ht = $item['unit_price_ht'];
                $line_item->vat = $item['vat'];

                // Calcul du total HT et TTC pour chaque line_item (en supposant que chaque line_item compte pour 1 unité)
                $line_total_ht = $line_item->unit_price_ht;
                $vat_amount = ($line_item->unit_price_ht * $line_item->vat / 100);
                $line_total_ttc = $line_total_ht + $vat_amount;

                // Mise à jour des montants totaux
                $devis->total_amount_ht += $line_total_ht;
                $devis->total_vat += $vat_amount;
                $devis->total_amount_ttc += $line_total_ttc;

                // Affectation des valeurs calculées au line_item
                $line_item->line_total_ht = $line_total_ht;
                $line_item->unit_price_ttc = $line_total_ttc;

                // Attribution de l'ID de la devis
                $line_item->devis_id = $devis->id;

                // Sauvegarde du line_item
                $line_item->save();
            }

            // Sauvegarde finale de la devis avec les montants mis à jour
            $devis->save();
            $userEmailFrom = auth()->user()->email;
            Mail::to($devis->sent_to)->send(new DevisMail($devis, $userEmailFrom));
            // Mail::from(auth()->user()->email)->to($devis->sent_to)->send(new DevisMail($devis));
            return $this->succesResponse($devis, 'devis créée avec succès');
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Create devis number with the prefix FAC-year-id 
     */
    function generateUniqueIdentifier()
    {
        // Préfixe fixe
        $prefix = 'DEV';
        // Récupérer l'année actuelle
        $year = Carbon::now()->year;
        // Récupérer le dernier numéro devis généré pour l'année en cours
        $lastIdentifier = DB::table('devis')
            ->where('devis_number', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();
        // Extraire l'identifiant numérique du dernier identifiant généré
        if ($lastIdentifier) {
            $lastNumber = (int) substr($lastIdentifier->devis_number, strrpos($lastIdentifier->devis_number, '-') + 1);
        } else {
            $lastNumber = 0;
        }
        // Incrémenter le numéro pour générer un nouvel identifiant
        $newNumber = $lastNumber + 1;
        // Générer l'identifiant final
        $newIdentifier = $prefix . '-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        return $newIdentifier;
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $devis = Devis::with('Devis_Line_items')->find($id);
            if (!$devis) {
                return $this->errorResponse("Cette devis n'existe pas");
            } else {
                return $this->succesResponse($devis, "Details de cette devis");
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditDevisRequest $request, $id)
    {
        try {
            $devis = Devis::find($id);
            if (!$devis) {
                return $this->errorResponse('Cette devis n\'existe pas');
            }
            // Mise à jour des informations de la devis
            $devis->client_id = $request->client_id;
            $devis->due_date = $request->due_date;
            $devis->special_conditions = $request->special_conditions;
            $devis->internal_notes = $request->internal_notes;

            // Variables pour les totaux
            $total_amount_ht = 0;
            $total_vat = 0;
            $total_amount_ttc = 0;

            // Identifiants des `line_items` existants
            $existingItemIds = $devis->devis_line_items->pluck('id')->toArray();
            $updatedItemIds = [];

            // Traitement des nouveaux `line_items`
            foreach ($request->line_items_devis as $item) {
                // Assurez-vous que la clé 'id' est définie
                $line_item = isset($item['id']) ? Devis_line_items::find($item['id']) : null;

                if ($line_item) {
                    // Mise à jour d'un `line_item` existant
                    $line_item->title = $item['title'] ?? $line_item->title;
                    $line_item->date = $item['date'] ?? $line_item->date;
                    $line_item->description = $item['description'] ?? $line_item->description;
                    $line_item->unit_price_ht = $item['unit_price_ht'] ?? $line_item->unit_price_ht;
                    $line_item->vat = $item['vat'] ?? $line_item->vat;
                    $line_item->discount = $item['discount'] ?? $line_item->discount;
                } else {
                    // Création d'un nouveau `line_item`
                    $line_item = new Devis_Line_items();
                    $line_item->devis_id = $devis->id;
                    $line_item->title = $item['title'];
                    $line_item->date = $item['date'];
                    $line_item->description = $item['description'];
                    $line_item->unit_price_ht = $item['unit_price_ht'];
                    $line_item->vat = $item['vat'];
                    $line_item->discount = $item['discount'];
                }

                // Calcul des totaux
                $line_total_ht = $line_item->unit_price_ht - $line_item->discount;
                $vat_amount = ($line_total_ht * $line_item->vat) / 100;
                $line_total_ttc = $line_total_ht + $vat_amount;

                // Mise à jour des attributs
                $line_item->line_total_ht = $line_total_ht;
                $line_item->unit_price_ttc = $line_total_ttc;
                $line_item->save();

                // Mise à jour des totaux
                $total_amount_ht += $line_total_ht;
                $total_vat += $vat_amount;
                $total_amount_ttc += $line_total_ttc;

                // Ajout de l'ID du `line_item` mis à jour
                $updatedItemIds[] = $line_item->id;
            }

            // Suppression des `line_items` obsolètes
            $obsoleteItemIds = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($obsoleteItemIds)) {
                Devis_Line_items::destroy($obsoleteItemIds);
            }

            // Mise à jour des montants totaux dans la devis
            $devis->total_amount_ht = $total_amount_ht;
            $devis->total_vat = $total_vat;
            $devis->total_amount_ttc = $total_amount_ttc;
            $devis->save();

            return response()->json([
                'success' => true,
                'message' => 'La devis a été mise à jour avec succès',
                'data' => $devis,
            ]);
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
            $devis = Devis::find($id);
            if (!$devis) {
                return $this->errorResponse("Cette devis n'existe pas");
            } else {
                $devis->delete();
                return response()->json([
                    "status" => 200,
                    "message" => "Devis supprimée"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * List Devis for the Trainer logged
     */
    public function ListDevisTrainer()
    {
        try {
            $user = auth()->user();
            $devis = $user->devis;
            if (!$devis->isEmpty()) {
                return $this->succesResponse($devis, "La liste des devis pour l'utilisateur connecté");
            } else {
                return response()->json([
                    "status" => true,
                    "message" => "la liste des devis est vide"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

}
