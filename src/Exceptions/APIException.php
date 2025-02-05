<?php

namespace InterWorks\Tableau\Exceptions;

use Exception;

class APIException extends Exception
{
    /** @var integer */
    protected $statusCode;
    /** @var string */
    protected $errorMessage;

    /**
     * APIException constructor.
     *
     * @param string|null $message    The error message for the API exception.
     * @param integer     $statusCode The status code for the API exception.
     *
     * @return void
     */
    public function __construct(?string $message = null, int $statusCode = 500)
    {
        // Set default values if not provided
        $this->statusCode = $statusCode;
        $this->errorMessage = $message ?? 'An error occurred with the Tableau API';

        // Call the base Exception constructor
        parent::__construct($this->errorMessage, $this->statusCode);
    }

    /**
     * Get the error message for the API exception
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Get the status code for the API exception
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
