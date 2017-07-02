<?php

namespace Modules\Plaid;

use Lightning\Tools\Communicator\RestClient;
use Lightning\Tools\Configuration;

class PlaidClient extends RestClient {
    public function __construct($server_address = '') {
        parent::__construct(Configuration::get('debug') ? 'https://sandbox.plaid.com/' : 'https://production.plaid.com');
        $this->sendJSON();
        $this->set('secret', Configuration::get('modules.plaid.secret'));
        $this->set('client_id', Configuration::get('modules.plaid.client_id'));
    }
}
