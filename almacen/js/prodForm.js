$(document).ready(function(){

  $('#addProdForm').on('change', '#newConsumePiece' ,function(){
    var unit = $('option:selected', this).attr('data-unit');
    var area = $('option:selected', this).attr('data-area');

    console.log('unit = '+unit);
    console.log('\narea = '+area);

    $('#newConsumeDesc').val($(this).find('option:selected').attr('data-desc'));
    
    $('select[id=newToolArea]').val(area);
    $('select[id=newConsumeUnit]').val(unit);

    if(typeof unit !='undefined'){
        $('#newConsumeUnit').attr('disabled',true);
    }
    else{
        $('#newConsumeUnit').attr('disabled', false);
    }
    if(typeof area != 'undefined'){
        $('#newToolArea').attr('disabled', true);
    }
    else{
        $('#newToolArea').attr('disabled', false);
    }

    $('#newToolArea').selectpicker('refresh');
    $('#newConsumeUnit').selectpicker('refresh');

  });



$('#addProdForm').submit(function(e){
        e.preventDefault();
        $('#newToolArea').attr('disabled', false);
        $('#newConsumeUnit').attr('disabled', false);
        $.ajax({
            url: 'php/moreproduct.php',
            type: 'POST',
            dataType: 'HTML',
            data: $('#addProdForm').serialize(),

            success: function(data){
                if(data == 1){
                    $div = $('<div id="stockExito" title="EXITO"></div>');
                    $div.append('<p><b>La entrada al almacén se ha registrado con éxito.</b></p>');

                    $div.dialog({
                        modal: true,
                        maxHeight:500,
                    }).prev(".ui-dialog-titlebar").css("background", "green");
                    
                    $('#newToolForm').trigger("reset");
                    $('#showStock').load('php/getinvent.php');
                }
                else{
                    $div = $('<div id="stockError" title="ERROR"></div>');
                    $div.append('<p><b>'+data+'</b></p>');

                    console.log(data);

                    $div.dialog({
                        modal: true,
                        maxHeight:500,
                    }).prev(".ui-dialog-titlebar").css("background", "red");
                    
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert("Algo pasó. Informar al encargado de Desarrollo [Something Bad Happened (NewToolForm). Console for Details]");
                console.log('error(s): '+textStatus, errorThrown);
            }

        });
    }); 

})