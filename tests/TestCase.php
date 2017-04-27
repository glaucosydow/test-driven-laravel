<?php

namespace Tests;

use App\Exceptions\Handler;
use App\Exceptions\ExceptionHandler;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function disableExceptionHandling()
    {
    	$this->app->instance(ExceptionHandler::class, new class extends Handler {
    		public function __construct() {}
    		public function report(\Exception $e) {}
    		public function render($request, \Exception $e) {
    			throw $e;
    		}
    	});
    }

    /**
     * Directly throws an exception if present in the
     * given instance of TestResponse.
     *
     * @param TestResponse $response
     *
     * @throws \Exception
     */
    protected function throwExceptionIfInResponse(TestResponse $response)
    {
        if (isset($response->exception)) {
            throw $response->exception;
        }
    }
}
