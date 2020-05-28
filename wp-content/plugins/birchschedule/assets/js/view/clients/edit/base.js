(function($){
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;

    var ns = namespace('appointer.view.clients.edit');

    addAction('appointer.initAfter', function(){
        appointer.view.initCountryStateField('birs_client_country', 'birs_client_state');
    });
})(jQuery);