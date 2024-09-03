<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facture\AddFactureRequest;
use App\Http\Requests\Facture\EditFactureRequest;
use App\Mail\FactureMail;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Line_items;
use App\Models\User;
use App\Traits\apiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class FactureController extends Controller
{
    use apiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $facture = Facture::with('line_items')->get();
            if (!$facture->isEmpty()) {
                return $this->succesResponse($facture, 'Liste des factures');
            } else {
                return response()->json([
                    "status" => 201,
                    "message" => "La liste des factures est vide"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddFactureRequest $request)
    {
        try {
            $facture = new Facture();
            $facture->facture_number = $this->generateUniqueIdentifier();
            $client = Client::find($request->client_id);
            if (!$client) {
                return $this->errorResponse('Le client sélectionné n\'existe pas', 400);
            }
            $facture->client_id = $client->id;
            $facture->user_id = auth()->user()->id;
            $facture->status = $request->status;
            $facture->due_date = $request->due_date;
            $facture->payment_method = $request->payment_method;
            $facture->internal_notes = $request->internal_notes;
            $facture->sent_to = $client->email;
            $facture->auto_reminder = $request->auto_reminder;

            // Initialisation des montants totaux
            $facture->total_amount_ht = 0;
            $facture->total_vat = 0;
            $facture->total_amount_ttc = 0;

            // Sauvegarde initiale de la facture pour obtenir l'ID
            $facture->save();

            // Boucle pour récupérer tous les line_items
            foreach ($request->line_items as $item) {
                // Création du line_item
                $line_item = new Line_items();
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
                $facture->total_amount_ht += $line_total_ht;
                $facture->total_vat += $vat_amount;
                $facture->total_amount_ttc += $line_total_ttc;

                // Affectation des valeurs calculées au line_item
                $line_item->line_total_ht = $line_total_ht;
                $line_item->unit_price_ttc = $line_total_ttc;

                // Attribution de l'ID de la facture
                $line_item->facture_id = $facture->id;

                // Sauvegarde du line_item
                $line_item->save();
            }

            // Sauvegarde finale de la facture avec les montants mis à jour
            $facture->save();
            Mail::to($facture->sent_to)->send(new FactureMail($facture));
            return $this->succesResponse($facture, 'Facture créée avec succès');
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Create Facture number with the prefix FAC-year-id 
     */
    function generateUniqueIdentifier()
    {
        // Préfixe fixe
        $prefix = 'FAC';
        // Récupérer l'année actuelle
        $year = Carbon::now()->year;
        // Récupérer le dernier numéro facture généré pour l'année en cours
        $lastIdentifier = DB::table('factures')
            ->where('facture_number', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();
        // Extraire l'identifiant numérique du dernier identifiant généré
        if ($lastIdentifier) {
            $lastNumber = (int) substr($lastIdentifier->facture_number, strrpos($lastIdentifier->facture_number, '-') + 1);
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
            $facture = Facture::with('line_items')->find($id);
            if (!$facture) {
                return $this->errorResponse("Cette facture n'existe pas");
            } else {
                return $this->succesResponse($facture, "Details de cette facture");
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditFactureRequest $request, $id)
    {
        try {
            $facture = Facture::find($id);
            if (!$facture) {
                return $this->errorResponse('Cette facture n\'existe pas');
            }
            // Mise à jour des informations de la facture
            $facture->client_id = $request->client_id;
            $facture->status = $request->status;
            $facture->due_date = $request->due_date;
            $facture->payment_method = $request->payment_method;
            $facture->internal_notes = $request->internal_notes;
            $facture->auto_reminder = $request->auto_reminder;

            // Variables pour les totaux
            $total_amount_ht = 0;
            $total_vat = 0;
            $total_amount_ttc = 0;

            // Identifiants des `line_items` existants
            $existingItemIds = $facture->line_items->pluck('id')->toArray();
            $updatedItemIds = [];

            // Traitement des nouveaux `line_items`
            foreach ($request->line_items as $item) {
                // Assurez-vous que la clé 'id' est définie
                $line_item = isset($item['id']) ? Line_items::find($item['id']) : null;

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
                    $line_item = new Line_items();
                    $line_item->facture_id = $facture->id;
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
                Line_items::destroy($obsoleteItemIds);
            }

            // Mise à jour des montants totaux dans la facture
            $facture->total_amount_ht = $total_amount_ht;
            $facture->total_vat = $total_vat;
            $facture->total_amount_ttc = $total_amount_ttc;
            $facture->save();

            return response()->json([
                'success' => true,
                'message' => 'La facture a été mise à jour avec succès',
                'data' => $facture,
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
            $facture = Facture::find($id);
            if (!$facture) {
                return $this->errorResponse("Cette facture n'existe pas");
            } else {
                $facture->delete();
                return response()->json([
                    "status" => 200,
                    "message" => "Facture supprimée"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * List Facture for the Trainer logged
     */
    public function ListFactureTrainer(){
        try {
            $user = auth()->user();
            $facture = $user->facture;
            if (!$facture->isEmpty()) {
                return $this->succesResponse($facture, "La liste des factures pour l'utilisateur connecté");
            } else {
                return response()->json([
                    "status" => true,
                    "message" => "la liste des factures est vide"
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
