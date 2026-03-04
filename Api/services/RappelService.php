<?php
namespace modeleApi\Service;

use modeleApi\Gateway\RappelGateway;

class RappelService {

    private $gateway;

    public function __construct(RappelGateway $gateway) {
        $this->gateway = $gateway;
    }

    public function getAll() {
        return $this->gateway->getAll();
    }
}