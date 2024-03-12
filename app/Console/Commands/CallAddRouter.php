<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CallAddRouter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:add';

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
     * @return int
     */
    public function handle()
    {
        $url = env('APP_URL') . '/add';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: XSRF-TOKEN=eyJpdiI6ImxsZFIxQU92Uk1mcVBmalZ1eXNTM2c9PSIsInZhbHVlIjoiYkxUVWl4RVQvcHFOZWUrb09zT0lJb29sMGd4Q2d5L281MWRNNU5WbkVTNUkybDR1RmJIUzA4ZDhKYlNCNndMNFRPK2grWG1Sd1VuUE1oaG9pOEU3dE43Y25XcmJ0RHlJWm0rZlBtRVpvQ2ZvajlYaUJJWHYvM3ArTWRaemM0cW0iLCJtYWMiOiIxMTY2NjFiOGE3YzM1NTMyNTQyZGFlNzlhMThjNDk2YTEzMzJiM2Y4ZmY0Y2Y2ZDFmMzU5YWI4MDgyYjY4ZTM0In0%3D; laravel_session=eyJpdiI6IncxSEVNalBLSW5GNlVwQzc5TzRYMVE9PSIsInZhbHVlIjoiaFNPMExLNlhNdzVOTGFoSGxiS3R4eE9RNGdOeURzeG9KTTl3RCtQY0k3UHFJTXlqRk5xendxOW93Rmo0RXg3aEhkQTF4TVlhT2hXY1IrTzJyOVFEZXM2TkpYT1BPa0ptcVM4UnBJZVVXU01hbWYxU2pJenJPSWRyL3dERyt3RHkiLCJtYWMiOiJiN2ZkOTVhZTVkZDI1NGQxYWNkYjk4OGUzYjU5YTNkZWE1ZTkxOWNiYmJiN2VkZDg5YTBkNWU1NjVmZmVjNmZiIn0%3D'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
}
