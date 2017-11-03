<?php

namespace Antares\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class ResponseHelper {

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $url;

    /**
     * @var bool
     */
    protected $notified = false;

    /**
     * ResponseHelper constructor.
     * @param string $type
     * @param string $message
     * @param string $url (optional)
     */
    public function __construct(string $type, string $message, string $url = null) {
        $this->type     = $type;
        $this->message  = $message;
        $this->url      = $url;
    }

    /**
     * Creates a response.
     *
     * @param string $type
     * @param string $message
     * @param string $url (optional)
     * @return ResponseHelper
     */
    public static function make(string $type, string $message, string $url = null) : ResponseHelper {
        return new static($type, $message, $url);
    }

    /**
     * Creates a response with success type.
     *
     * @param string $message
     * @param string $url (optional)
     * @return ResponseHelper
     */
    public static function success(string $message, string $url = null) : ResponseHelper {
        return new static('success', $message, $url);
    }

    /**
     * Creates a response with error type.
     *
     * @param string $message
     * @param string $url (optional)
     * @return ResponseHelper
     */
    public static function error(string $message, string $url = null) : ResponseHelper {
        return new static('error', $message, $url);
    }

    /**
     * Creates a response with warning type.
     *
     * @param string $message
     * @param string $url (optional)
     * @return ResponseHelper
     */
    public static function warning(string $message, string $url = null) : ResponseHelper {
        return new static('warning', $message, $url);
    }

    /**
     * Creates a response with info type.
     *
     * @param string $message
     * @param string $url (optional)
     * @return ResponseHelper
     */
    public static function info(string $message, string $url = null) : ResponseHelper {
        return new static('info', $message, $url);
    }

    /**
     * Returns an array of the object.
     *
     * @return array
     */
    public function toArray() : array {
        return [
            'type'      => $this->type,
            'message'   => $this->message,
            'url'       => $this->url,
            'notified'  => $this->notified,
        ];
    }

    /**
     * Sets URL for response.
     *
     * @param string|null $url
     */
    public function setUrl(string $url = null) : void {
        $this->url = $url;
    }

    /**
     * Returns JSON response.
     *
     * @return JsonResponse
     */
    public function json() : JsonResponse {
        return response()->json($this->toArray());
    }

    /**
     * Send the message to the session flash.
     *
     * @return ResponseHelper
     */
    public function notify() : ResponseHelper {
        $this->notified = true;

        app('antares.messages')->add($this->type, $this->message);

        return $this;
    }

    /**
     * Returns redirect response with notification.
     *
     * @return RedirectResponse
     */
    public function redirect() : RedirectResponse {
        if( ! $this->notified) {
            $this->notify();
        }

        $url = $this->url ?: app(UrlGenerator::class)->previous();

        return response()->redirectTo($url);
    }

    /**
     * Returns dedicated response based on the given request.
     *
     * @param Request $request
     * @return Response
     */
    public function resolve(Request $request) : Response {
        if($request->expectsJson()) {
            return $this->json();
        }

        return $this->redirect();
    }

    /**
     * Determines if the response has been recognized as failed.
     *
     * @return bool
     */
    public function isFailed() : bool {
        return in_array($this->type, ['error', 'warning'], true);
    }

    /**
     * Determines if the response has been recognized as success.
     *
     * @return bool
     */
    public function isOk() : bool {
        return in_array($this->type, ['success', 'info'], true);
    }

    /**
     * Returns the message.
     *
     * @return string
     */
    public function getMessage() : string {
        return $this->message;
    }

}
