<?php

namespace App\Console\Commands;

use App\Mail\BilanMail;
use App\Models\Bilan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAnnualBilans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-annual-bilans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        if ($today->isSameDay(Carbon::create($today->year, 9, 6))) {

            $bilans = Bilan::all();
            foreach ($bilans as $bilan) {
                $user = User::find($bilan->user_id);
                if ($user) {
                    Mail::to($user->email)->send(new BilanMail($bilan));
                }
            }

            $this->info('Tous les bilans ont été envoyés pour la fin de l\'année.');
        } else {
            $this->info('Ce n\'est pas encore la fin de l\'année.');
        }
    }
}
