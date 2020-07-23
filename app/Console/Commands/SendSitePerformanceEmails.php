<?php

namespace App\Console\Commands;

use App\User;
use App\Mail\UserSitePerformance;
use Illuminate\Console\Command;

class SendSitePerformanceEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:site-performance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $users = User::whereNotNull('client_id')->cursor();
        
        foreach($users as $user){
            Mail::to($user)->send(new UserSitePerformance($user));
        }
    }
}
