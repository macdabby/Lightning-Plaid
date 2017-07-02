<?php

namespace Modules\Plaid\API;

use Lightning\Tools\ClientUser;
use Lightning\Tools\Request;
use Lightning\View\API;
use Modules\Plaid\PlaidClient;
use Modules\Stripe\Model\StripeCustomer;
use Modules\Stripe\StripeClient;

class Connect extends API {
    /**
     * Connect a client to a stripe account.
     */
    public function post() {
        $institution = Request::get('institution');
        $account_name = Request::get('account_name');

        $plaid = new PlaidClient();
        $plaid->set('public_token', Request::get('public_token'));
        $plaid->callPost('item/public_token/exchange');
        $access_token = $plaid->get('access_token');

        $plaid->unset('public_token');
        $plaid->set('account_id', Request::get('account_id'));
        $plaid->set('access_token', $access_token);
        $plaid->callPost('processor/stripe/bank_account_token/create');
        $stripe_account_id = $plaid->get('stripe_bank_account_token');

        if (!$stripe_account_id) {
            throw new \Exception('Could not link bank account. Please try again.');
        }

        $user = ClientUser::getInstance();
        $customer = StripeCustomer::loadById($user->id);
        $source_id = $customer->attachSource($stripe_account_id, [
            'institution' => $institution,
            'account_name' => $account_name,
        ]);

        return [
            'id' => $source_id,
            'description' => $institution . ' - ' . $account_name,
        ];
    }
}
