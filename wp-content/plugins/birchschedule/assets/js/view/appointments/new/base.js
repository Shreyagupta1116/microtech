(function($){
    var params = appointer_view_appointments_new;
    var locationsMap = params.locations_map;
    var locationsOrder = params.locations_order;
    var locationsStaffMap = params.locations_staff_map;
    var staffOrder = params.staff_order;
    var locationsServicesMap = params.locations_services_map;
    var servicesStaffMap = params.services_staff_map;
    var servicesOrder = params.services_order;
    var servicePriceMap = params.services_prices_map;
    var serviceDurationMap = params.services_duration_map;

    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;
    var addAction = birchpress.addAction;

    var ns = namespace('appointer.view.appointments.new');

    defineFunction(ns, 'setLocationOptions', function(){
        var options = appointer.model.getLocationOptions(locationsMap, locationsOrder);
        var html = appointer.view.getOptionsHtml(options);
        $('#birs_appointment_location').html(html);
    });

    defineFunction(ns, 'setServiceOptions', function(){
        var locationId = $('#birs_appointment_location').val();
        var options = appointer.model.getServiceOptions(locationsServicesMap, 
            locationId, servicesOrder);
        var html = appointer.view.getOptionsHtml(options);

        var serviceId = $('#birs_appointment_service').val();
        $('#birs_appointment_service').html(html);

        if(serviceId && _(options.order).has(serviceId)) {
            $('#birs_appointment_service').val(serviceId);
        }
    });

    defineFunction(ns, 'setStaffOptions', function() {
        var locationId = $('#birs_appointment_location').val();
        var serviceId = $('#birs_appointment_service').val();
        var options = appointer.model.getStaffOptions(locationsStaffMap, servicesStaffMap, 
            locationId, serviceId, staffOrder);
        var html = appointer.view.getOptionsHtml(options);

        var staffId = $('#birs_appointment_staff').val();
        $('#birs_appointment_staff').html(html);

        if(staffId && _(options.order).has(staffId)) {
            $('#birs_appointment_staff').val(staffId);
        }
    });


    defineFunction(ns, 'setLocation', function() {
        var appointmentLocationId = Number($('#birs_appointment_location')
            .attr('data-value'));
        if(appointmentLocationId) {
            $('#birs_appointment_location').val(appointmentLocationId);
        }
    });

    defineFunction(ns, 'setStaffValue', function() {
        var appointmentStaffId = Number($('#birs_appointment_staff')
            .attr('data-value'));
        if(appointmentStaffId) {
            $('#birs_appointment_staff').val(appointmentStaffId);
        }
    });

    defineFunction(ns, 'setDuration', function(){
        var serviceId = $('#birs_appointment_service').val();
        if(serviceId) {
            var duration = serviceDurationMap[serviceId]['duration'];
            if(duration !== null || duration !== undefined){
                $('#birs_appointment_duration').val(duration);
            }
        }
    });

    defineFunction(ns, 'ifOnlyShowAvailable', function() {
        return false;
    });

    defineFunction(ns, 'initDatepicker', function(){
        var config = {
            ifOnlyShowAvailable: ns.ifOnlyShowAvailable
        };
        return appointer.view.initDatepicker(config);
    });

    defineFunction(ns, 'initClientInfo', function() {
        appointer.view.initCountryStateField('birs_client_country', 'birs_client_state');
    });

    defineFunction(ns, 'reloadTimeOptions', function(){

    });

    defineFunction(ns, 'schedule', function() {
        var ajaxUrl = appointer.model.getAjaxUrl();
        var i18nMessages = appointer.view.getI18nMessages();
        var postData = $('form').serialize();
        postData += '&' + $.param({
            action: 'appointer_view_appointments_new_schedule'
        });
        $.post(ajaxUrl, postData, function(data, status, xhr){
            var result = appointer.model.parseAjaxResponse(data);
            if(result.errors) {
                appointer.view.showFormErrors(result.errors);
            } 
            else if(result.success) {
                var url = $.parseJSON(result.success.message).url;
                window.location = _.unescape(url);
            }
            $('#birs_appointment_actions_schedule').val(i18nMessages['Schedule']);
            $('#birs_appointment_actions_schedule').prop('disabled', false);
        });
        $('#birs_appointment_actions_schedule').val(i18nMessages['Please wait...']);
        $('#birs_appointment_actions_schedule').prop('disabled', true);
    });

    defineFunction(ns, 'init', function(){
        var ajaxUrl = appointer.model.getAjaxUrl();

        ns.setLocationOptions();
        ns.setLocation();
        ns.setServiceOptions();
        ns.setStaffOptions();
        ns.setStaffValue();
        ns.setDuration();

        var datepicker = ns.initDatepicker();
        defineFunction(ns, 'refreshDatepicker', function(){
            appointer.view.refreshDatepicker(datepicker);
        });

        ns.reloadTimeOptions();

        $('#birs_appointment_location').change(function() {
            ns.setServiceOptions();
            ns.setStaffOptions();
            ns.setDuration();
            ns.refreshDatepicker();
            ns.reloadTimeOptions();
        });

        $('#birs_appointment_service').change(function(){
            ns.setStaffOptions();
            ns.setDuration();
            ns.refreshDatepicker();
            ns.reloadTimeOptions();
        });

        $('#birs_appointment_staff').change(function(){
            ns.refreshDatepicker();
            ns.reloadTimeOptions();
        });

        $('#birs_appointment_date').on('change', function(){
            ns.reloadTimeOptions();
        });

        ns.initClientInfo();

        $('#birs_appointment_actions_schedule').click(ns.schedule);
    });
    addAction('appointer.initAfter', ns.init);

})(jQuery);