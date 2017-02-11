$(document).ready(function(){


$('#newToolForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'php/newTool.php',
            type: 'POST',
            dataType: 'HTML',
            data: $('#newToolForm').serialize(),

            success: function(data){
                if(data == 1){
                    $div = $('<div id="newToolSuccess" title="EXITO"></div>');
                    $div.append('<p><b>La herramienta ha sido registrada correctamente.</b></p>');

                    $div.dialog({
                        modal: true,
                        maxHeight:500,
                    }).prev(".ui-dialog-titlebar").css("background", "green");
                    
                    $('#newToolForm').trigger("reset");
                }
                else{
                    $div = $('<div id="newToolFail" title="ERROR"></div>');
                    $div.append('<p><b>'+data+'</b></p>');

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