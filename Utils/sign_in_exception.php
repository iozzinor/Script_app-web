<?php
    class SignInException extends Exception implements JsonSerializable
    {
        private $domain_;
        private $message_;

        public function __construct($domain, $message)
        {
            $this->domain_  = $domain;
            $this->message_ = $message;
            
            parent::__construct($message);
        }

        public function jsonSerialize()
        {
            return [
                'domain'    => $this->domain_,
                'error'     => $this->message_
            ];
        }
    }
?>