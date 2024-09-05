<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\BilanMail;
use App\Models\Bilan;
use App\Models\Facture;
use App\Models\Session;
use App\Models\User;
use App\Traits\apiResponseTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class BilanController extends Controller
{
    use apiResponseTrait;

    public function generateBilansForAllYears($userId)
    {
        // 1. Récupérer toutes les années où l'utilisateur a eu des sessions ou des factures
        $years = Session::where('user_id', $userId)
            ->selectRaw('YEAR(date) as year')
            ->groupBy('year')
            ->pluck('year')
            ->merge(
                Facture::where('user_id', $userId)
                    ->selectRaw('YEAR(date) as year')
                    ->groupBy('year')
                    ->pluck('year')
            )
            ->unique(); // On évite les doublons d'années

        // 2. Générer un bilan pour chaque année trouvée
        $bilans = [];
        foreach ($years as $year) {
            $bilans[] = $this->generateBilanForYear($userId, $year);
        }

        return response()->json($bilans);
    }

    private function generateBilanForYear($userId, $year)
    {
        $totalInvoicesHT = Facture::where('user_id', $userId)
            ->whereYear('date', $year)
            ->sum('amount_ht');

        $sessions = Session::where('user_id', $userId)
            ->whereYear('date', $year)
            ->get();

        $totalSessionHours = 0;
        $totalTrainees = 0;

        foreach ($sessions as $session) {
            $totalSessionHours += $session->duration * $session->number_of_trainees;
            $totalTrainees += $session->number_of_trainees;
        }

        return Bilan::updateOrCreate(
            ['year' => $year, 'user_id' => $userId],
            [
                'total_invoices_ht' => $totalInvoicesHT,
                'total_session_hours' => $totalSessionHours,
                'total_sessions' => $sessions->count(),
                'total_trainees' => $totalTrainees
            ]
        );
    }

    public function generateBilanForCurrentYear()
    {
        $user = auth()->user();

        $currentYear = Carbon::now()->year;

        $totalInvoicesHT = Facture::where('user_id', $user->id)
            ->whereYear('due_date', $currentYear)
            ->sum('total_amount_ht');

        $sessions = Session::where('user_id', $user->id)
            ->whereYear('date', $currentYear)
            ->get();

        $totalSessionHours = 0;
        $totalTrainees = 0;

        foreach ($sessions as $session) {
            $totalSessionHours += $session->duration * $session->number_of_trainees;
            $totalTrainees += $session->number_of_trainees;
        }

        $bilan = Bilan::updateOrCreate([
            'year' => $currentYear,
            'user_id' => $user->id
        ], [
            'total_session_hours' => $totalSessionHours,
            'total_invoices_ht' => $totalInvoicesHT,
            'total_session' => $sessions->count(),
            'total_trainees' => $totalTrainees,
        ]);

        return $this->succesResponse($bilan, "Bilan de l'année {$bilan->year} pour {$user->email}");
    }

    // public function allBilan()
    // {
    //     $today = Carbon::now();
    //     if ($today->isSameDay(Carbon::create($today->year, 9, 5))) {

    //         $bilans = Bilan::all();
    //         foreach ($bilans as $bilan) {
    //             // $bilan->user_id->user->email;
    //             $user = User::find($bilan->user_id);
    //             Mail::to($user->email)->send(new BilanMail($bilan));
    //         }
    //         return $this->succesResponse($bilans, 'Tous les bilans ont été envoyés pour la fin de l\'année.');

    //     } else {
    //         return $this->errorResponse('Ce n\'est pas encore la fin de l\'année.', 200);
    //     }
    // }

}
