<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CallExampleRouter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        $client = new \GuzzleHttp\Client();

        try {
            $url = env('APP_URL').'/example';
            $responseExample = $client->get($url);

            $statusCodeExample = $responseExample->getStatusCode();
            $responseBodyExample = $responseExample->getBody()->getContents();

            $this->info("HTTP status code for /example: $statusCodeExample");
            $this->info("Response body for /example: $responseBodyExample");
        } catch (\Exception $e) {
            $this->error("Error occurred while calling /example: " . $e->getMessage());
        }

        return 0;
    }
}
