<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:debug';
    protected $description = 'Commande de test pour afficher une variable avec dd';
    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
     $val = "Test console";
        dd($val); 
    }
}
