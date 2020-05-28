(function($){
    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;

    var ns = namespace('appointer.view.appointments.edit.clientlist.edit');

    defineFunction(ns, 'render', function(viewState) {
        appointer.view.appointments.edit.clientlist.render.fn.default(viewState);
        var clientId = viewState.clientId;
        if(viewState.view === 'edit') {
            var row = $('#birs_client_list_row_' + clientId);
            var editRow = $('#birs_client_list_row_edit_' + clientId);

            var data = editRow.attr('data-edit-html');
            editRow.find('td').html(data);
            ns.initForm();
            row.hide();
            editRow.show();
            birchpress.util.scrollTo(editRow);
        }
    });

    defineFunction(ns, 'save', function() {
        var ajaxUrl = appointer.model.getAjaxUrl();
        var i18nMessages = appointer.view.getI18nMessages();
        var save_button = $('#birs_appointment_client_edit_save');
        var postData = $('form').serialize();
        postData += '&' + $.param({
            action: 'appointer_view_appointments_edit_clientlist_edit_save'
        });
        $.post(ajaxUrl, postData, function(data, status, xhr){
            var result = appointer.model.parseAjaxResponse(data);
            if(result.errors) {
                appointer.view.showFormErrors(result.errors);
            } 
            else if(result.success) {
                window.location.reload();
            }
            save_button.val(i18nMessages['Save']);
            save_button.prop('disabled', false);
        });
        save_button.val(i18nMessages['Please wait...']);
        save_button.prop('disabled', true);
    });

    defineFunction(ns, 'initForm', function() {
        appointer.view.initCountryStateField('birs_client_country', 'birs_client_state');
        $('#birs_appointment_client_edit_cancel').click(function(){
            appointer.view.appointments.edit.clientlist.setViewState({
                view: 'list'
            });
        });
        $('#birs_appointment_client_edit_save').click(function(){
        	ns.save();
        });
    });

    defineFunction(ns, 'init', function() {
        appointer.view.appointments.edit.clientlist.render.fn.when('edit', ns.render);
    	$('.wp-list-table.birs_clients .row-actions .edit a').click(function(eventObject){
            var clientId = $(eventObject.target).attr('data-item-id');
            appointer.view.appointments.edit.clientlist.setViewState({
                view: 'edit',
                clientId: clientId
            });
    	});
    });

    addAction('appointer.initAfter', ns.init);

})(jQuery);