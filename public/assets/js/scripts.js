
function scroll_to_class(element_class, removed_height) {
	var scroll_to = $(element_class).offset().top - removed_height;
	if($(window).scrollTop() != scroll_to) {
		$('html, body').stop().animate({scrollTop: scroll_to}, 0);
	}
}


jQuery(document).ready(function() {

    $("#type_client").select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Selccionar tipo cliente'
    });

    $("#type_dni").select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Selccionar tipo documento'
    });

    $("#city").select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Selccionar ciudad'
    });

    $("#sex").select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Selccionar sexo'
    });

    $("#bank").select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Selccionar banco'
    });

    $("#type_acount_bank").select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Selccionar tipo cuenta'
    });
	
    /*
        Fullscreen background
    */
    $.backstretch("assets/img/backgrounds/1.jpg");
    
    $('#top-navbar-1').on('shown.bs.collapse', function(){
    	$.backstretch("resize");
    });
    $('#top-navbar-1').on('hidden.bs.collapse', function(){
    	$.backstretch("resize");
    });
    
    /*
        Form
    */
    $('.f1 fieldset:first').fadeIn('slow');
    
    $('.f1 input[type="text"], .f1 input[type="password"], .f1 textarea').on('focus', function() {
    	$(this).removeClass('input-error');
    });

    var exp_number = /^[a-zA-Z0-9]{6,15}$/;
    var exp_acount = /^[0-9]{7,}$/;
    var exp_names =/^[a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ\s]+$/;
    var exp_date = /^([0-9]{2}\/[0-9]{2}\/[0-9]{4})$/;
    var exp_address = /^[0-9a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ#.\-_\s]+$/;
    var exp_phone = /^[0-9-()+]{3,20}$/;
    var exp_email = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9-]{2,}[.][a-zA-Z]{2,4}$/;

    var icono_paso_tres = '<div id="two" class="f1-step">\n' +
        '                            <div class="f1-step-icon"><i class="fa fa-check-square-o"></i></div>\n' +
        '                            <p>Tus documentos</p>\n' +
        '                        </div>';

    var prime = '<div class="form-group">\n' +
        '                            <label class="form-check-label">\n' +
        '                                <input id="prime" name="prime" class="form-check-input" type="checkbox">\n' +
        '                                Usuario Prime\n' +
        '                            </label>\n' +
        '                        </div>';

    var contract = '<div class="form-group">\n' +
        '                            <label for="contrato" class="form-check-label">\n' +
        '                                <input type="checkbox" id="contract" name="contract" required/>\n' +
        '                                Contrato <a href="pagina_condiciones.html">terminos</a>\n' +
        '                            </label>\n' +
        '                        </div>';
    var terms = '<div class="form-group">\n' +
        '                            <label for="condiciones" class="form-check-label">\n' +
        '                                <input  type="checkbox" id="terms" name="terms" required/>\n' +
        '                                ¿Acepta <a href="pagina_condiciones.html">terminos</a> y condiciones?\n' +
        '                            </label>\n' +
        '                        </div>';


    var tipo_cliente = $("#type_client").val();

    $("#four .btn-submit").hide();

    if (tipo_cliente.length > 0 && tipo_cliente != 83) {

        $("#two").remove();

        $("#four .btn-next").hide();

        $("#four .btn-submit").show();

        $("#two").remove();

        var padre = $("#password_confirmation").parent();

        padre.after("" +contract + terms + "");

        $("#four .btn-next").hide();

        $("#four .btn-submit").show();

        var parent_fieldset = $("#rut").parents('fieldset');

        parent_fieldset.find('input[type="checkbox"]').each(function () {
            $(this).parent().remove();
        });

        $('.f1-step').css({
            'width': '50%'
        });
    }

    $('#type_client').on('change', function() {

        var valor = $(this).val();

        if (valor != 83) {

            $("#two").remove();

            var padre = $("#password_confirmation").parent();

            padre.after("" + contract + terms + "");

            $("#four .btn-next").hide();

            $("#four .btn-submit").show();

            var parent_fieldset = $("#rut").parents('fieldset');

            parent_fieldset.find('input[type="checkbox"]').each(function () {
                $(this).parent().remove();
            });

            $('.f1-step').css({
                'width': '50%',
            });

        } else {

            $("#two").remove();

            $("#one").after(icono_paso_tres);

            $("#four .btn-next").show();

            $("#four .btn-submit").hide();

            var parent_fieldset = $("#rut").parents('fieldset');

            parent_fieldset.find('input[type="checkbox"]').each(function () {
                $(this).parent().remove();
            });


            parent_fieldset.find('input[type="checkbox"]').each(function () {
                $(this).parent().remove();
            });

            var padre = $("#rut").parent();

            padre.after("" + prime + "" +contract + terms + "");

            var parent_fieldset = $("#password_confirmation").parents('fieldset');

            parent_fieldset.find('input[type="checkbox"]').each(function () {
                $(this).parent().remove();
            });

            $('.f1-step').css({
                'width': '33.3%'
            });

        }
    });

    $('#bank').on('change', function() {


        var bank = $(this).val();
        var type_acount = $("#type_acount_bank").val();
        var acount = $("#acount").val();

        if (type_acount.length == 0 && bank.length == 0 && acount.length === 0) {

            $(this).removeClass('input-error');
            $("#type_acount_bank").removeClass('input-error');
            $("#acount").removeClass('input-error');
        }

    });

    $('#type_acount_bank').on('change', function() {

        var type_acount= $(this).val();
        var bank = $("#bank").val();
        var acount = $("#acount").val();

        if (type_acount.length == 0 && bank.length == 0 && acount.length === 0) {

            $(this).removeClass('input-error');
            $("#bank").removeClass('input-error');
            $("#acount").removeClass('input-error');
        }
    });


    // next step
    $('.f1 .btn-next').on('click', function() {

    	var parent_fieldset = $(this).parents('fieldset');
    	var next_step = true;
    	// navigation steps / progress steps
    	var current_active_step = $(this).parents('.f1').find('.f1-step.active');
    	var progress_line = $(this).parents('.f1').find('.f1-progress-line');
    	
    	// fields validation

    	parent_fieldset.find('input[type="text"], input[type="email"], input[type="number"], input[type="tel"], input[type="date"], input[type="password"], select').each(function() {

            if ($(this).attr('id') == 'type_client') {

                if( $(this).val() == "" ) {

                    $(this).addClass('input-error');
                    next_step = false;

                }
                else {
                    $(this).removeClass('input-error');
                }
            }

            if ($(this).attr('id') == 'type_dni') {

                if($(this).val() == ""  ) {

                    $(this).addClass('input-error');
                    next_step = false;

                }
                else {
                    $(this).removeClass('input-error');

                }
            }

            if ($(this).attr('id') == 'dni') {

                var number = $(this).val();

                if (exp_number.test(number)) {

                    $(this).removeClass('input-error');
                }
                else {
                    $(this).addClass('input-error');

                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'city') {

                if( $(this).val() == "" ) {

                    $(this).addClass('input-error');
                    next_step = false;

                }
                else {
                    $(this).removeClass('input-error');

                }
            }

            if ($(this).attr('id') == 'sex') {

                if( $(this).val() == "" ) {

                    $(this).addClass('input-error');
                    next_step = false;

                }
                else {
                    $(this).removeClass('input-error');

                }
            }

            if ($(this).attr('id') == 'first-name') {

                var first_name = $(this).val();

                if (exp_names.test(first_name) && first_name.length > 2 ) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'last-name') {

                var last_name = $(this).val();

                if (exp_names.test(last_name) && last_name.length > 2 ) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'birthday') {

                var date = $(this).val();

                if (exp_date.test(date) && date.length == 10  ) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'address') {

                var address = $(this).val();

                if (exp_address.test(address) && address.length > 5 ) {

                    $(this).removeClass('input-error');
                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'phone') {

                var phone = $(this).val();

                if (exp_phone.test(phone) && (phone.length == 7 || phone.length == 10 || phone.length == 13)) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'code') {

                var code = $(this).val();

                if (exp_number.test(code)) {

                    $(this).removeClass('input-error');
                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'email') {

                var email = $(this).val();

                if (exp_email.test(email) && email.length > 0) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'password') {

                var password = $(this).val();

                if (password.length > 5) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }

            if ($(this).attr('id') == 'password_confirmation') {

                var password_confirmation = $(this).val();
                var password = $("#password").val();

                if (password_confirmation.length > 5 && password_confirmation == password) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    next_step = false;
                }
            }


    	});

    	// fields validation
    	
    	if( next_step ) {

    		parent_fieldset.fadeOut(400, function() {
    			// change icons
    			current_active_step.removeClass('active').addClass('activated').next().addClass('active');

    			// show next step
	    		$(this).next().fadeIn();
	    		// scroll window to beginning of the form
    			scroll_to_class( $('.f1'), 20 );
	    	});
    	}
    	
    });
    
    // previous step
    $('.f1 .btn-previous').on('click', function() {
    	// navigation steps / progress steps
    	var current_active_step = $(this).parents('.f1').find('.f1-step.active');
    	var progress_line = $(this).parents('.f1').find('.f1-progress-line');
    	
    	$(this).parents('fieldset').fadeOut(400, function() {
    		// change icons
    		current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');

    		// show previous step
    		$(this).prev().fadeIn();
    		// scroll window to beginning of the form
			scroll_to_class( $('.f1'), 20 );
    	});
    });

    // submit
    $('.f1').on('submit', function(e) {
    	
    	// fields validation
    	$(this).find('input[type="checkbox"], input[type="number"], input[type="text"], input[type="email"], input[type="password"], select').each(function() {

    	    
            if ($(this).attr('id') == 'contract') {

                if( $(this).is(':checked')  ) {
                    $(this).parent().removeClass('input-error');

                }else {
                    $(this).parent().addClass('input-error');

                    e.preventDefault();
                }
            }


            if ($(this).attr('id') == 'code') {

                var code = $(this).val();

                if (exp_number.test(code)) {

                    $(this).removeClass('input-error');
                }
                else {

                    $(this).addClass('input-error');

                    e.preventDefault();
                }
            }

            if ($(this).attr('id') == 'email') {

                var email = $(this).val();

                if (exp_email.test(email) && email.length > 0) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    e.preventDefault();
                }
            }

            if ($(this).attr('id') == 'password') {

                var password = $(this).val();

                if (password.length > 5) {

                    $(this).removeClass('input-error');
                }
                else {

                    $(this).addClass('input-error');
                    e.preventDefault();
                }
            }

            if ($(this).attr('id') == 'password_confirmation') {

                var password_confirmation = $(this).val();
                var password = $("#password").val();

                if (password_confirmation.length > 5 && password_confirmation == password) {

                    $(this).removeClass('input-error');

                }
                else {

                    $(this).addClass('input-error');
                    e.preventDefault();
                }
            }

            if ($(this).attr('id') == 'terms') {

                if( $(this).is(':checked')  ) {
                    $(this).parent().removeClass('input-error');

                }else {
                    $(this).parent().addClass('input-error');
                    e.preventDefault();
                }
            }

            if ($(this).attr('id') == 'bank') {

                var bank = $(this).val();
                var type_acount_bank = $("#type_acount_bank").val();
                var acount = $("#acount").val();

                if( bank != "") {

                    if (acount.length == 0){

                        $("#acount").addClass('input-error');
                        e.preventDefault();

                    }else{

                        $("#acount").removeClass('input-error');
                    }

                    if (type_acount_bank.length == 0){

                        $("#type_acount_bank").addClass('input-error');
                        e.preventDefault();

                    }else{

                        $("#type_acount_bank").removeClass('input-error');
                    }

                }
            }

            if ($(this).attr('id') == 'type_acount_bank') {

                var type_acount_bank = $(this).val();
                var acount = $("#acount").val();
                var bank =  $("#bank").val();

                if(type_acount_bank != "" ) {

                    if (bank.length == 0){
                        $("#bank").addClass('input-error');
                        e.preventDefault();
                    }else{
                        $("#bank").removeClass('input-error');
                    }

                    if (acount.length == 0){
                        $("#acount").addClass('input-error');
                        e.preventDefault();
                    }else{
                        $("#acount").removeClass('input-error');
                    }

                }
            }

            if ($(this).attr('id') == 'acount') {

                var acount= $(this).val();
                var bank = $("#bank").val();
                var type_acount_bank = $("#type_acount_bank").val();


                if (acount != "") {

                    if(exp_acount.test(acount)) {

                        $(this).removeClass('input-error');

                        if (bank == 0 && type_acount_bank.length == 0){


                            $("#bank").addClass('input-error');
                            $("#type_acount_bank").addClass('input-error');
                            e.preventDefault();

                        }

                        if (bank.length == 0 && type_acount_bank.length > 0){

                            $("#bank").addClass('input-error');
                            $("#type_acount_bank").removeClass('input-error');
                            e.preventDefault();

                        }

                        if (bank.length > 0 && type_acount_bank.length == 0){

                            $("#type_acount_bank").addClass('input-error');
                            $("#bank").removeClass('input-error');
                            e.preventDefault();

                        }

                        if (bank.length > 0 && type_acount_bank.length > 0){

                            $("#type_acount_bank").removeClass('input-error');
                            $("#bank").removeClass('input-error');

                        }

                    } else {

                        $(this).addClass('input-error');

                        e.preventDefault();
                    }
                }
            }

            //e.preventDefault();

    	});
    	// fields validation
    	
    });

});
