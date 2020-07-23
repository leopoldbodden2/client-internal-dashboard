<?php

namespace App\Console\Commands;

use App\CdyneMessage;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReadIncomingCdyneMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms-message:read-incoming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read Incoming Cdyne Messages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user_query = User::whereNotNull('cdyne_license_key')->whereNotNull('cdyne_phone');
        $bar = $this->output->createProgressBar($user_query->count());

        foreach($user_query->cursor() as $user) {
            $sms_message = new CdyneMessage(['user_id' => $user->id]);
            // $sms_message->SetPostbackURLForLicenseKey();
            $start_date = Carbon::now()->startOfYear();
            $end_date = Carbon::now()->endOfYear();

            $unread_only = false;
            $incoming_messages = $sms_message->ReadIncomingMessages($start_date, $end_date, $unread_only);
            dd($incoming_messages);
            $bar->advance();
        }

        $bar->finish();
    }
}
