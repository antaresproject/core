<?php



namespace Antares\Extension\Model;

class Operation {

    /**
     * @var string
     */
    protected $message;

    /**
     * Operation constructor.
     * @param string $message
     */
    public function __construct(string $message) {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage() : string {
        return $this->message;
    }

}
