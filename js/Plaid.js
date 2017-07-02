(function() {
    if (lightning.modules.plaid) {
        return;
    }
    var self = lightning.modules.plaid = {
        inited: false,
        init: function(){
            if (self.inited) {
                return;
            }

            self.linkHandler = Plaid.create({
                env: 'sandbox',
                clientName: 'Stripe/Plaid Test',
                key: lightning.get('modules.plaid.public_key'),
                product: ['auth'],
                selectAccount: true,
                apiVersion: 'v2',
                onSuccess: function(public_token, metadata) {
                    // Send the public_token and account ID to your app server.
                    console.log('public_token: ' + public_token);
                    console.log('account ID: ');
                    console.log(metadata);
                    self.getStripeToken(public_token, metadata);
                },
                onExit: function(err, metadata) {
                    // The user exited the Link flow.
                    if (err !== null) {
                        // The user encountered a Plaid API error prior to exiting.
                    }
                },
            });
        },
        connect: function(){
            lightning.js.require('https://cdn.plaid.com/link/v2/stable/link-initialize.js', function(){
                self.init();
                self.linkHandler.open();
            });
        },
        getStripeToken: function(public_token, metadata) {
            $.ajax({
                url: '/api/plaid/connect',
                type: 'POST',
                data: {
                    'public_token': public_token,
                    'institution': metadata.institution.name,
                    'account_id': metadata.account_id,
                    'account_name': metadata.account.name,
                },
                success: function(data){
                    lightning.modules.stripe.addAndSelectBankOption(data.id, data.description);
                },
                persist_dialog: true
            });
        }
    };
})();
