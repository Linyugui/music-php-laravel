<?php

namespace App\Console\Commands;

use App\Models\SimilarSongModel;
use Illuminate\Console\Command;

class DailySimilarSong extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:SimilarSong';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '相似歌曲每日推荐';

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
        $this->info("\n************************ Start...");
        try{
            SimilarSongModel::generateDailySimilarSong(10,2,20);
        }catch (\Exception $e){
            $this->error($e->getMessage());
            $this->info("\n************************ failed!");
        }
        $this->info("\n************************successfully!");
        return ;
    }
}
