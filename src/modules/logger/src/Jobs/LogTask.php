<?php

namespace Antares\Logger\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Prettus\RequestLogger\ResponseLogger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailer;
use App\Jobs\Job;

class LogTask extends Job implements ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    /**
     * Request instance
     *
     * @var Request 
     */
    protected $request;

    /**
     * Response instance
     *
     * @var Response 
     */
    protected $response;

    /**
     * Create a new job instance.
     *
     * @param  Request  $request
     * @param  Response $response
     * @return void
     */
    public function __construct($request, $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle()
    {
        $requestLogger = app(ResponseLogger::class);
        $requestLogger->log($this->request, $this->response);
    }

}
