//$(function(){
	/***********Reglas*************************/

	//Función para validar si es un empleado
	// function esEmpleadoInterno(value, element, param){
	// 	var codEmpleado =$('#tbAcciones').attr('data-codpersonal');
	// 	if (value == codEmpleado && value !== '') {
	// 		return true;
	// 	}
	// 	else{
	// 		return false;
	// 	}
	// }

	//$.validator.addMethod('esEmpleado', esEmpleadoInterno, 'El código de empleado no existe');

	/* CUSTOM Date Validator */                
	var isDate = function(value) {
		var validDate = /^(\d{2})\/(\d{2})\/(\d{4})?$/;    
		return validDate.test(value);
	}    
	$.validator.addMethod("isdate", function(value, element) {
		return isDate(value);
	});
	
	$('#frmPlanificacion').validate({
		highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
		errorElement: 'span',
	    errorClass: 'help-block',
	    errorPlacement: function(error, element) {
	        if(element.parent('.input-group').length) {
	            error.insertAfter(element.parent());
	        } else {
	            error.insertAfter(element);
	        }
	    },
		rules: {
			//txtEmpleado : {esEmpleado: true},
			txtTotal: {
				max: 100,
				min: 100
			}
		},
		messages: {
			txtTotal: $('#hdnTotalWeight').val()
		}
	});

	//Reglas para los elementos de la tabla dinámica
	var reglaObjetivo = {
        required: true,
        messages: {
            required: $('#hdnEnterTarget').val()
        }
    }
    var reglaActividad = {
        required: true,
        messages: {
            required: $('#hdnEnterActivity').val()
        }
    }
    var reglaCronograma = {
        required: true,
        date: false,
        isdate: true,
        messages: {
            required: $('#hdnEnterChronogram').val(),
            date: $('#hdnCorrectChronogram').val()
        }
    }
    var reglaMeta = {
        required: true,
        messages: {
            required: $('#hdnEnterGoal').val()
        }
    }
    var reglaPesoRelativo = {
        required: true,
        number: true,
        min: 10,
        max: 50,
        messages: {
            required: $('#hdnEnterWeight').val(),
            number: $('#hdnNumericWeight').val(),
            min: jQuery.format($('#hdnMinWeight').val()+' {0}'),
            max: jQuery.format($('#hdnMaxWeight').val()+' {0}')
        }
    }
    var reglaAccion = {
        required: true,
        messages: {
            required: $('#hdnEnterProposed').val()
        }
    }
    

    //Estos métodos deben ir después de .validate()
    $('.Objetivo').each(function() {
        $(this).rules('add', reglaObjetivo);
    });
    $('.Actividad').each(function() {
        $(this).rules('add', reglaActividad);
    });
    $('.Cronograma').each(function() {
        $(this).rules('add', reglaCronograma);
        $(this).datepicker($.datepicker.regional[$('#hdnLanguage').val()]);
    });
    $('.Meta').each(function() {
        $(this).rules('add', reglaMeta);
    });
    $('.PesoRelativo').each(function() {
        $(this).rules('add', reglaPesoRelativo);
    });
    $('.Accion').each(function() {
        $(this).rules('add', reglaAccion);
    });

    /*************Fin de Reglas***********************/


    function str2slug(str) {
		//Se remueve acentos, intercambia ñ por n, etc
		var from = "áàäâÁÀÂÄéèëêÉÈÊËíìïîÍÌÏÎóòöôÓÒÖÔúùüûÚÙÛÜñÑçÇ";
		var to   = "aaaaAAAAeeeeEEEEiiiiIIIIooooOOOOuuuuUUUUnNcC";
		
		for (var i=0, l=from.length ; i<l ; i++) {
			str = str.replace(
				new RegExp(from.charAt(i), 'g'),
					to.charAt(i)
				);
		}
		return str;
	}

	function imprimirInstructivo(lenguaje){
		if(lenguaje==='es')
			$('#ifrPdf').attr('src', 'docs/INSTRUCTIVO.pdf');
		else
			$('#ifrPdf').attr('src', 'docs/INSTRUCTIVE.pdf');
        $('#divPdf').modal('show');
	}

	function imprimirPlanificacion(cod, per){
		if(!$('#tbObjetivos').attr('data-guardado'))
			guardarPlanificacion(true, false);
		$.post('procesar_imprimir.php', {tarea: 'imprimirPlanificacion', codigo: cod, periodo: per}, function(){
            $('#ifrPdf').attr('src', 'docs/'+$.trim($('#hdnPlanning').val())+' '+$('#txtEmpleado').val()+'.pdf');
            $('#divPdf').modal('show');
            $('#tbObjetivos').attr('data-guardado', 1);
        });
	}

	function buscarPersonal(codper, accion){
		$.ajax({
			type: "POST",
			url: "procesar_buscar.php",
			dataType: 'json',
			data: {tarea: 'buscarPersonal', codpersonal: codper},
			success:
			function(respuesta){

				if (accion === 'planificacion'){
					$('#txtPais').val(respuesta.pais);
					$('#txtEmpleado').val(respuesta.codpersonal);
					$('#txtCargo').val(respuesta.cargo);
					$('#txtApellido').val(respuesta.apellidos);
					$('#txtNombre').val(respuesta.nombres);
					$('#txtArea').val(respuesta.area);
					$('#txtOficina').val(respuesta.oficina);
					$('#txtAntiguedadCargo').val(respuesta.anoscargo);
					$('#txtAntiguedadPM').val(respuesta.anospm);
					$('#txtSuperiorInmediatoApellido').val(respuesta.apellidossuperior);
					$('#txtSuperiorInmediatoNombre').val(respuesta.nombressuperior);
					$('#txtSuperiorInmediatoCargo').val(respuesta.cargosuperior);
					$('#txtSuperiorFuncionalApellido').val(respuesta.apellidosfuncional);
					$('#txtSuperiorFuncionalNombre').val(respuesta.nombresfuncional);
					$('#txtSuperiorFuncionalCargo').val(respuesta.cargofuncional);
					$('#txtIncidenciaObjetivo').val(respuesta.incidenciaobjetivo);
					$('#txtIncidenciaLiderazgo').val(respuesta.incidencialiderazgo);
				}else if (accion === 'modificacion'){
					$("#sltPais option").filter(function() {
					    return $(this).text() == respuesta.pais; 
					}).prop('selected', true);
					$('input[name="hdnCodPersonal"]').val(respuesta.codpersonal);
					$('#txtApellido').val(respuesta.apellidos);
					$('#txtApellido').autocomplete({
						disabled: true
					});
					$('#txtNombre').val(respuesta.nombres);
					cargarPaises(respuesta.codpais);
					$('#sltArea option').filter(function(index) {
						return $(this).text() === respuesta.area; 
					}).prop('selected', true);
					cargarAreas(respuesta.codarea);
					$("#sltCargo option").filter(function() {
					    return $(this).text() == respuesta.cargo; 
					}).prop('selected', true);
					cargarRegionales(respuesta.codregional);
					$("#sltRegional option").filter(function() {
					    return $(this).text() == respuesta.regional; 
					}).prop('selected', true);
					$("#sltOficina option").filter(function() {
					    return $(this).text() == respuesta.oficina; 
					}).prop('selected', true);
					$('#txtFechaNacimiento').val(respuesta.fechanacimiento);
					$('#txtFechaIngreso').val(respuesta.fechaingreso);
					$('#txtFechaCargo').val(respuesta.fechacargo);
					$("#sltTipoDni option").filter(function() {
					    return $(this).text() == respuesta.tipodi; 
					}).prop('selected', true);
					$('#txtDni').val(respuesta.di);
					$('#txtEmail').val(respuesta.email);
					$('#divMotivo').removeClass('hide');
					$('#hdnCodSuperior').val(respuesta.codpersonalsuperior);
					$('#txtSuperiorInmediatoApellido').val(respuesta.apellidossuperior);
					$('#txtSuperiorInmediatoNombre').val(respuesta.nombressuperior);
					$('#hdnCodFuncional').val(respuesta.codpersonalfuncional);
					$('#txtSuperiorFuncionalApellido').val(respuesta.apellidosfuncional);
					$('#txtSuperiorFuncionalNombre').val(respuesta.nombresfuncional);
					$('#sltGrupo option').filter(function() {
					    return $(this).val() == respuesta.grupo; 
					}).prop('selected', true);
				}else if (accion === 'eliminacion'){
					$('input[name="hdnCodPersonal"]').val(respuesta.codpersonal);
					$('#txtApellidoBaja').val(respuesta.apellidos);
					$('#txtNombreBaja').val(respuesta.nombres);
					$('#txtCargoBaja').val(respuesta.cargo);				
					$('#txtAreaBaja').val(respuesta.area);
				}

			}
		});

	}

	function buscarPersonalCargo(){
		$.ajax({
			type: "POST",
			url: "procesar_buscar.php",
			dataType: 'json',
			data: {tarea: 'buscarPersonalCargo'},
			success:
			function(respuesta){
				if(respuesta!=null){
					$('#divPersonalCargo').removeClass('hide');
					var appendPersonal = '';
					var estado;
					$.each(respuesta, function(i) {
					estado = (respuesta[i].estado=='REGISTRADA')? '<label class="label label-warning">'+$('#hdnRegistrada').val()+'</label>' :
								 (respuesta[i].estado=='APROBADA')?'<label class="label label-success">'+$('#hdnAprobada').val()+'</label>' :
								  '<label class="label label-danger">'+$('#hdnSinRegistro').val()+'</label>';
						if(respuesta[i].estado=='REGISTRADA'){
							$('hdnEstado').val('<label class="label label-warning">{% set registered = textos.registered %} {% trans registered %}</label>'); 
						}
						appendPersonal = '<tr class="trPersonalCargo"> <td><label for=""  class="control-label">'+respuesta[i].apellidos+' '+respuesta[i].nombres+'</label></td></td> <td style="text-align:center;">'+estado+'</td> <td><button name="codpersonal" value="'+respuesta[i].codpersonal+'" class="btn btn-default btn-mini" onClick="pasarCodigo()"><i class="glyphicon glyphicon-pencil"></i></button></td> </tr>';
						$('#tbPersonalCargo').find('tbody').append(appendPersonal);

					});
				}
							
			}
		});

	}

	function pasarCodigo(){
		$('codpersonal').val();
		$('#frmPersonalCargo').submit();
	}


	function buscarPlanificacion(){
		var codper = $('#txtEmpleado').val();//$('#tbAcciones').attr('data-codpersonal');
		var anno = $('#sltPeriodo').val();

		var $contenidoAjax = $('#lnkBuscarPlanificacion').html('<img src="images/ajax-loader.gif"/>');

		$.post('procesar_buscar.php', {tarea: 'buscarPlanificacion', codpersonal: codper, periodo: anno}, 
			function(respuesta){
				
				var datos = JSON.parse(respuesta);
				if(datos !== null){

					var total = 0;
					var numFilaObjetivo = 0;
					var longObj = 0;
					var numFilaAccion = 0;
					var longAcc = 0;

					if(datos.mejora[0].estado !== "APROBADA"){

		 				$.each(datos.mejora, function() {

		 					$('#tbObjetivos').attr('data-codplanificacion', this.codplanificacion);
		 					$('#txtActitudes').val(this.mejora);
		 					$('#txtActitudes').attr('data-codmejora', this.codmejora);
		 					$('#txtCompromisos').val(this.compromiso);

					    });
		 				numFilaObjetivo = datos.objetivos.length;
		 				longObj = numFilaObjetivo-1;
		 				$('#tbObjetivos').attr('data-numFilas', numFilaObjetivo);
		 				$('#tbObjetivos').attr('data-ultItem', longObj);
		 				$('#tbObjetivos').attr('data-objObtenido', 1);

					    $.each(datos.objetivos, function(i) {

					    	if(i>=longObj){
					    		var appendTxt = '<tr class="trObjetivos"> <input type="hidden" class="CodObjetivo" name="hdnCodObjetivo['+i+']"> <td class="form-group col-md-3"><input type="text" class="form-control input-sm required Objetivo vModal" name="txtObjetivo['+i+']" data-toggle="modal" data-target="#divInputData"></td> <td class="form-group col-md-3"><input type="text" class="form-control input-sm required Actividad vModal" name="txtActividad['+i+']" data-toggle="modal" data-target="#divInputData"></td> <td class="form-group col-md-1"><input type="text" class="form-control input-sm required Cronograma Fecha" name="txtCronograma['+i+']"></td> <td class="form-group col-md-3"><input type="text" class="form-control input-sm required Meta vModal" name="txtMeta['+i+']" data-toggle="modal" data-target="#divInputData"></td> <td class="form-group col-md-2"><div class="input-group input-group-sm"><input type="text" class="form-control required PesoRelativo" name="txtPesoRelativo['+i+']"><span class="input-group-addon">%</span></div></td> <td><a class="addObjetivo btn btn-success btn-mini" data-toggle="tooltip" data-original-title="'+$('#hdnAddTarget').val()+'"><i class="glyphicon glyphicon-plus-sign"></i></a></td> </tr>';
								$('.trObjetivos:last').after(appendTxt);

					    	}
					    	else  if(i>0){
								var appendTxt = '<tr class="trObjetivos"> <input type="hidden" class="CodObjetivo" name="hdnCodObjetivo['+i+']"> <td class="form-group col-md-3"><input type="text" class="form-control input-sm required Objetivo vModal" name="txtObjetivo['+i+']" data-toggle="modal" data-target="#divInputData"></td> <td class="form-group col-md-3"><input type="text" class="form-control input-sm required Actividad vModal" name="txtActividad['+i+']" data-toggle="modal" data-target="#divInputData"></td> <td class="form-group col-md-1"><input type="text" class="form-control input-sm required Cronograma Fecha" name="txtCronograma['+i+']"></td> <td class="form-group col-md-3"><input type="text" class="form-control input-sm required Meta vModal" name="txtMeta['+i+']" data-toggle="modal" data-target="#divInputData"></td> <td class="form-group col-md-2"><div class="input-group input-group-sm"><input type="text" class="form-control required PesoRelativo" name="txtPesoRelativo['+i+']"><span class="input-group-addon">%</span></div></td> <td><a class="delObjetivo btn btn-danger btn-mini" data-toggle="tooltip" data-original-title="'+$('#hdnDelTarget').val()+'"><i class="glyphicon glyphicon-minus-sign"></i></a></td> </tr>';
								$('.trObjetivos:last').after(appendTxt);
								
							}else{
								$('#tbObjetivos').find('.trObjetivos').prepend('<input type="hidden" class="CodObjetivo" name="hdnCodObjetivo['+i+']">');
								$('.addObjetivo').attr('class','delObjetivo btn btn-danger btn-mini');
								$('.delObjetivo').attr('data-toggle','tooltip');
								$('.delObjetivo').attr('data-original-title', $('#hdnDelTarget').val());
								$('.delObjetivo').html('<i class="glyphicon glyphicon-minus-sign"></i>');
							}
							$('input[name="txtObjetivo['+i+']"]').rules('add', reglaObjetivo);
							$('input[name="txtActividad['+i+']"]').rules('add', reglaActividad);
							$('input[name="txtCronograma['+i+']"]').datepicker($.datepicker.regional[$('#hdnLanguage').val()]);
							$('input[name="txtCronograma['+i+']"]').rules('add', reglaCronograma);
							$('input[name="txtMeta['+i+']"]').rules('add', reglaMeta);
							$('input[name="txtPesoRelativo['+i+']"]').rules('add', reglaPesoRelativo);
							$('.delObjetivo').tooltip();
							$('.addObjetivo').tooltip();

					        $.each(this, function(key, valor) {
					       		switch(key){
					       			case 'codobjetivo':
					       				$('input[name="hdnCodObjetivo['+i+']"]').val(valor);
					                	break;
					                case 'objetivo':
					                    $('input[name="txtObjetivo['+i+']"]').val(valor);
					                	break;
					                case 'actividad':
					                	$('input[name="txtActividad['+i+']"]').val(valor);
					                	break;
					                case 'cronograma':
					                	$('input[name="txtCronograma['+i+']"]').val(valor);
					                	break;
					                case 'meta':
					                	$('input[name="txtMeta['+i+']"]').val(valor);
					                	break;
					                case 'pesorelativo':
					                	$('input[name="txtPesoRelativo['+i+']"]').val(valor);
					                	total += +valor;
					                	break;
					            }
					       	});
							
					    });

						$('#txtTotal').val(total).valid();

						numFilaAccion = datos.acciones.length;
		 				longAcc = numFilaAccion-1;
		 				$('#tbAcciones').attr('data-numFilas', numFilaAccion);
		 				$('#tbAcciones').attr('data-ultItem', longAcc);

					    $.each(datos.acciones, function(j) {
					    	if(longAcc > 0){
						    	if(j>=longAcc){
						    		var appendTxt = '<tr class="trAcciones"> <input type="hidden" class="CodAccion" name="hdnCodAccion['+j+']"><td class="form-group"><input type="text" class="form-control required Accion vModal" name="txtAccion['+j+']" data-toggle="modal" data-target="#divInputData"></td> <td><a class="addAccion btn btn-success btn-mini" data-toggle="tooltip" data-original-title="'+$('#hdnAddSkill').val()+'"><i class="glyphicon glyphicon-plus-sign"></i></a></td> </tr>';
									$('.trAcciones:last').after(appendTxt);
						    	}
						    	else if(j>0){
						    		var appendTxt = '<tr class="trAcciones"> <input type="hidden" class="CodAccion" name="hdnCodAccion['+j+']"><td class="form-group"><input type="text" class="form-control required Accion vModal" name="txtAccion['+j+']" data-toggle="modal" data-target="#divInputData"></td> <td><a class="delObjetivo btn btn-danger btn-mini" data-toggle="tooltip" data-original-title="'+$('#hdnDelSkill').val()+'"><i class="glyphicon glyphicon-minus-sign"></i></a></td> </tr>';
									$('.trAcciones:last').after(appendTxt);
						    	}else{
						    		$('#tbAcciones').find('.trAcciones').prepend('<input type="hidden" class="CodAccion" name="hdnCodAccion['+j+']">');
						    		$('.addAccion').attr('class','delAccion btn btn-danger btn-mini');
						    		$('.delAccion').attr('data-toggle','tooltip');
									$('.delAccion').attr('data-original-title', $('#hdnDelSkill').val());
									$('.delAccion').html('<i class="glyphicon glyphicon-minus-sign"></i>');
						    	}
					    	}

					    	
							$('[name="txtAccion['+j+']"]').rules('add', reglaAccion);
							$('.delAccion').tooltip();
							$('.addAccion').tooltip();


					        $.each(this, function(key, valor) {
					        	switch(key){
					        		case 'codaccion':
					       				$('input[name="hdnCodAccion['+j+']"]').val(valor);
					                	break;
					        		case 'accion':
					                    $('input[name="txtAccion['+j+']"]').val(valor);
					                	break;
					        	}  
					        });

					    });
						$('#lblConfirmTitle').text($('#hdnExistPlannigTitle').val());
						$('#lblConfirm').text($('#hdnExistPlanning').val());
						$('#divConfirm').modal('show');
					
					}else{
						$.each(datos.mejora, function() {
							$('#txtActitudes').val(this.mejora);
							$('#txtActitudes').prop('disabled', true);
							$('#txtCompromisos').val(this.compromiso);
							$('#txtCompromisos').prop('disabled', true);
						});
						$('.trObjetivos').remove();
						numFilaObjetivo = datos.objetivos.length;
		 				longObj = numFilaObjetivo-1;
		 				$('#tbObjetivos').attr('data-numFilas', numFilaObjetivo);
		 				$('#tbObjetivos').attr('data-ultItem', longObj);
		 				$('#tbObjetivos').attr('data-objObtenido', 1);
		 				$('#tbObjetivos').attr('data-aceptar', 1);

						$.each(datos.objetivos, function(i) {
							var appendObj = '<tr class="trObjetivos"> <td class="form-group col-md-3"><input type="text" class="form-control input-sm" name="txtObjetivo['+i+']" disabled ></td> <td class="form-group col-md-3"><input type="text" class="form-control input-sm" name="txtActividad['+i+']" disabled ></td> <td class="form-group col-md-1"><input type="text" class="form-control input-sm Fecha" name="txtCronograma['+i+']" disabled ></td> <td class="form-group col-md-3"><input type="text" class="form-control input-sm" name="txtMeta['+i+']" disabled ></td> <td class="form-group col-md-2"><div class="input-group input-group-sm"><input type="text" class="form-control" name="txtPesoRelativo['+i+']" disabled ><span class="input-group-addon">%</span></div></td></tr>';
							$('#tbObjetivos').find('tbody').append(appendObj);

							$.each(this, function(key, valor) {
					       		switch(key){
					       			case 'objetivo':
					                    $('input[name="txtObjetivo['+i+']"]').val(valor);
					                	break;
					                case 'actividad':
					                	$('input[name="txtActividad['+i+']"]').val(valor);
					                	break;
					                case 'cronograma':
					                	$('input[name="txtCronograma['+i+']"]').val(valor);
					                	break;
					                case 'meta':
					                	$('input[name="txtMeta['+i+']"]').val(valor);
					                	break;
					                case 'pesorelativo':
					                	$('input[name="txtPesoRelativo['+i+']"]').val(valor);
					                	total += +valor;
					                	break;
					            }

					       	});
						});
						$('#txtTotal').val(total);
						$('.trAcciones').remove();
						numFilaAccion = datos.acciones.length;
		 				longAcc = numFilaAccion-1;
		 				$('#tbAcciones').attr('data-numFilas', numFilaAccion);
		 				$('#tbAcciones').attr('data-ultItem', longAcc);
						$.each(datos.acciones, function(j) {
							var appendAcc = '<tr class="trAcciones"> <td class="form-group"><input type="text" class="form-control" name="txtAccion['+j+']" disabled></td> </tr>';
							$('#tbAcciones').find('tbody').append(appendAcc);

							$.each(this, function(key, valor) {
						       	switch(key){
						       		case 'accion':
						            	$('input[name="txtAccion['+j+']"]').val(valor);
						                break;
						        }  
						    });
						});
						$('#rootwizard').bootstrapWizard('show', 1);
						$('.pager').find('#lnkGuardar').addClass('hide');
						$('.pager').find('#lnkAprobar').addClass('hide');
					}

 				}
 				else
 					$('#rootwizard').bootstrapWizard('show', 1);
 				$contenidoAjax.html($('#hdnNext').val());
		});
		
	}


	function guardarPlanificacion(imp, envmail){
		/*if($('#tbObjetivos').attr('data-guardado') === 1)
			var envmail = false;
		else{	
			var envmail = true;
        }*/
		//Valores del Tab1
		var pai = $('#txtPais').val().substring(0,3);
                
        var emp = $('#txtEmpleado').val();
		
		var nom = $('#txtApellido').val().substring(0,1)+$('#txtNombre').val().substring(0,1);

		var anno = $('#sltPeriodo').val();

		//Valores del Tab2
		var obj = [];
		$('#tbObjetivos input.Objetivo').each(function() {
			obj.push($(this).val());
		});
		//var obje = obj + '';
		var act = [];
		$('#tbObjetivos input.Actividad').each(function (){
			act.push($(this).val());
		});
		//var activ = act + '';
		var cro = [];
		$('#tbObjetivos input.Cronograma').each(function (){
			cro.push($(this).val());
		}); 
		//var cron = cro + '';
		var met = [];
		$('#tbObjetivos input.Meta').each(function (){
			met.push($(this).val());
		});
		//var meta = met + '';
		var pes = [];
		$('#tbObjetivos input.PesoRelativo').each(function (){
			pes.push(parseInt($(this).val()));
		});
		//var peso = pes + '';
		//Valores del Tab3
		var acc = [];
		$('#tbAcciones input.Accion').each(function (){
			acc.push($(this).val());
		});
		//var acci = acc + '';
		var acti = $('#txtActitudes').val();
		var com = $('#txtCompromisos').val();
		//Valores del Tab4
		//var iob = $('#txtIncidenciaObjetivo').val();
		//var ili = $('#txtIncidenciaLiderazgo').val();

		if(parseInt($('#tbObjetivos').attr('data-objObtenido')) === 0){

			

			$.post("procesar_registrar.php", {tarea: "registrarPlanificacion", pais: pai, codpersonal: emp, nombre: nom, periodo: anno, objetivos: obj, actividades: act, cronogramas: cro, metas: met, pesos: pes, acciones: acc, actitud: acti, compromiso: com, enviarmail: envmail}, function(){
				if(envmail){
					$('#lblAlert').text($('#hdnSavedPlanning').val());
				 	$('#divAlert').modal('show');
				 	$('#tbObjetivos').attr('data-guardado', 1);
				}
			});
		}else{

			var codplan = $('#tbObjetivos').attr('data-codplanificacion');
			
			var codobj = [];
			$('#tbObjetivos input.CodObjetivo').each(function (){
				codobj.push($(this).val());
			});
			//var codobje = codobj + '';
                                               
			var codacc = [];
			$('#tbAcciones input.CodAccion').each(function (){
				codacc.push($(this).val());
			});
            //var codacci = codacc + '';
                   
			var codmej = $('#txtActitudes').attr('data-codmejora');
			//var codinc = $('#txtIncidenciaObjetivo').attr('data-codincidencia');

			$.post('procesar_registrar.php', {tarea: 'editarPlanificacion', pais: pai, codpersonal: emp, nombre: nom, periodo: anno, codplanificacion: codplan, codobjetivos: codobj, objetivos: obj, actividades: act, cronogramas: cro, metas: met, pesos: pes, codacciones: codacc, acciones: acc, codmejora: codmej, actitud: acti, compromiso: com, imprimir: imp, enviarmail: envmail}, function(){
				if(envmail){
					$('#lblAlert').text($('#hdnSavedPlanning').val());
				 	$('#divAlert').modal('show');
				 	$('#tbObjetivos').attr('data-guardado', 1);
				 	
				}
			});
			
		}
                
	}

	function aprobarPlanificacion(){
		if(!$('#tbObjetivos').attr('data-guardado'))
			guardarPlanificacion(false, false);
		var anno = $('#sltPeriodo').val();		
		var codplan = $('#tbObjetivos').attr('data-codplanificacion');
		var emp = $('#txtEmpleado').val();
		$.post('procesar_registrar.php', {tarea: 'aprobarPlanificacion', codplanificacion: codplan, codpersonal: emp, periodo: anno}, function() {
			$('#divConfirm').modal('hide');
			$('#lblAlert').text($('#hdnApprovalSaved').val());
			$('#divAlert').modal('show');
			$('#tbObjetivos').attr('data-guardado', 1);
		});
	}

	function listarPersonal(){
		if($('#hdnLanguage').val() == 'es'){
			$('#tbPersonal').dataTable( {
				"oLanguage": { "sUrl": "locale/textos/dataTables.spanish.txt" },
				"bSort": false,
				"bDestroy": true,
				"bServerSide": true,
				aoColumns: [
					null,
					null,
					null,
					null,
					{ "mRender": function (o) {return '<button name="btnEditar" class="btn btn-default btn-mini" value="'+o+'" data-toggle="modal" data-target="#divAltaPersonal"><i class="glyphicon glyphicon-pencil"></i></button>'}},
					{ "mRender": function (o) {return '<button name="btnEliminar" class="btn btn-default btn-mini" value="'+o+'" data-toggle="modal" data-target="#divBajaPersonal"><i class="glyphicon glyphicon-trash"></i></button>'}}
				],
				"dom": 'T<"clear">lfrtip',
				"tableTools": {
					"sSwfPath": "/swf/copy_csv_xls_pdf.swf",
					"aButtons": [ "copy",
                            {
                                "sExtends": "xls",
                                "sFileName": "TableTools.csv",
                                "bFooter": false
                            },
                            {
                                "sExtends": "pdf",
                                "sFileName": "TableTools.csv"
                            }
                        ]
				},
				"sAjaxSource": "procesar_buscar.php",
				"fnServerData": function ( sSource, aoData, fnCallback ) {
					aoData.push({"name": "tarea", "value": "listarPersonalPaginado" });
					$.ajax( {
						"dataType": 'json', 
						"type": "POST", 
						"url": sSource, 
						"data": aoData, 
						"success": fnCallback
					} );
				}
			} );
		}else{
			$('#tbPersonal').dataTable( {
				"bSort": false,
				"bDestroy": true,
				"bServerSide": true,
				aoColumns: [
					null,
					null,
					null,
					null,
					{ "mRender": function (o) {return '<button name="btnEditar" class="btn btn-default btn-mini" value="'+o+'" data-toggle="modal" data-target="#divAltaPersonal"><i class="glyphicon glyphicon-pencil"></i></button>'}},
					{ "mRender": function (o) {return '<button name="btnEliminar" class="btn btn-default btn-mini" value="'+o+'" data-toggle="modal" data-target="#divBajaPersonal"><i class="glyphicon glyphicon-trash"></i></button>'}}
				],
				"sAjaxSource": "procesar_buscar.php",
				"fnServerData": function ( sSource, aoData, fnCallback ) {
					aoData.push({"name": "tarea", "value": "listarPersonalPaginado" });
					$.ajax( {
						"dataType": 'json', 
						"type": "POST", 
						"url": sSource, 
						"data": aoData, 
						"success": fnCallback
					} );
				}
			} );
		}
	}

	function cargarPaises(paisElegido){
		$.ajax({
			async: false,
			url: 'procesar_buscar.php',
			type: 'POST',
			dataType: 'json',
			data:{tarea: 'cargarAreas', pais: paisElegido},
			success: function(respuesta){
	      		var areas = $('#sltArea');
	      		areas.empty();
	      		areas.append('<option value="">'+$('#hdnSelectArea').val()+'</option>');

	      		var regionales = $('#sltRegional');
	      		regionales.empty();
	      		regionales.append('<option value="">'+$('#hdnSelectRegional').val()+'</option>');

	      		$.each(respuesta, function(index, area) {
	      			if (typeof area.codarea !== 'undefined')
	            		areas.append('<option value='+area.codarea+'>' + area.area + '</option>');
	            });
	            $.each(respuesta, function(index, regional) {
	            	if (typeof regional.codregional !== 'undefined')
	            		regionales.append('<option value='+regional.codregional+'>' + regional.regional + '</option>');
	            });
	        }
		});

      	$('#txtApellido').focus();
	}

	function cargarAreas(areaElegida){
		$.ajax({
			async: false,
			url: 'procesar_buscar.php',
			type: 'POST',
			dataType: 'json',
			data: {tarea: 'cargarCargos', area: areaElegida, pais: $('#sltPais').val()},
			success: function(respuesta){
	      		var cargos = $('#sltCargo');
	      		cargos.empty();
	      		cargos.append('<option value="">'+$('#hdnSelectPosition').val()+'</option>');

	      		$.each(respuesta, function(index, cargo) {
	                // agregamos opciones al combo
	            	cargos.append('<option value='+cargo.codcargo+'>' + cargo.cargo + '</option>');
	            });
	        }
	    });
	}

	function cargarRegionales(regionalElegida){
		$.ajax({
			async: false,
			url: 'procesar_buscar.php',
			type: 'POST',
			dataType: 'json',
			data: {tarea: 'cargarOficinas', regional: regionalElegida},
			success:  function(respuesta){
	      		var oficinas = $('#sltOficina');
	      		oficinas.empty();
	      		oficinas.append('<option value="">'+$('#hdnSelectOffice').val()+'</option>');

	      		$.each(respuesta, function(index, oficina) {
	                // agregamos opciones al combo
	            	oficinas.append('<option value='+oficina.codoficina+'>' + oficina.oficina + '</option>');
	            });
	        }
		});
	}
