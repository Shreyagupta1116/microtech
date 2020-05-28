(function($){
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;

    var ns = namespace('appointer.view.appointments.edit.clientlist.cancel');

    defineFunction(ns, 'cancel', function(appointmentId, clientId) {
        var i18nMessages = appointer.view.getI18nMessages();
        var r = window.confirm(i18nMessages['Are you sure you want to cancel this appointment?']);
        if(r != true) {
            return;
        }
        var ajaxUrl = appointer.model.getAjaxUrl();
        var postData = $.param({
            action: 'appointer_view_appointments_edit_clientlist_cancel_cancel',
            birs_client_id: clientId,
            birs_appointment_id: appointmentId
        });
        $.post(ajaxUrl, postData, function(data, status, xhr){
            var result = appointer.model.parseAjaxResponse(data);
            if(result.success) {
                if(result.success.code === 'reload') {
                    window.location.reload();
                }
                else if(result.success.code === 'redirect_to_calendar') {
                    var url = $.parseJSON(result.success.message).url;
                    window.location = _.unescape(url);
                }
            }
        });    
    });

    defineFunction(ns, 'init', function() {
    	$('.wp-list-table.birs_clients .row-actions .cancel a').click(function(eventObject){
            var clientId = $(eventObject.target).attr('data-item-id');
            var appointmentId = $('#birs_appointment_id').val();
            ns.cancel(appointmentId, clientId);
    	});
    });

    addAction('appointer.initAfter', ns.init);
})(jQuery);