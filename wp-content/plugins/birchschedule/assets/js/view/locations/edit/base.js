(function($){
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;

    var ns = namespace('appointer.view.locations.edit');

    addAction('appointer.initAfter', function(){
        appointer.view.initCountryStateField('birs_location_country', 'birs_location_state');
	});
})(jQuery);