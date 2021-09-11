<?php

namespace App\Console\Commands;

use App\Luma\Client\LumaClient;
use App\Luma\Exception\HueException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateDeveloper extends Command
{
    protected $signature = 'luma:create-developer';

    protected $description = 'Creating developer in Philips Hue Bridge…';

    public function __construct(
        private LumaClient $lumaClient
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->comment($this->description);

        try {
            $response = $this->lumaClient->request(
                'POST',
                '/',
                '{"devicetype":"my_hue_app#iphone peter"}' // @TODO
            );

            dd($response->body()); // @TODO
        } catch (HueException $exception) {
            if ($exception->getCode() === HueException::ERROR_BUTTON_NOT_PRESSED) {
                $this->error('You must press the Philip Hue Bridge button and try again.');
            }
        }

        return 0;
    }
}
