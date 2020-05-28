(function($){

    var namespace = birchpress.namespace;
    var defineFunction = birchpress.defineFunction;

    var ns = namespace('appointer');
    
    defineFunction(ns, 'init', function(){});

    $(function(){
        appointer.init();
    });
})(jQuery);