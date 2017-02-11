$(document).ready(function(){
	$('#showStock').load('php/getinvent.php');

	$('#buttonEmpty').on('click', function(e){
        e.preventDefault();
        $('#showStock').load('php/filterEmptyLog.php');
        $('livestock').animate({ scrollTop: 0 }, 360);
    });

    $('#buttonDefault').on('click', function(e){
    	e.preventDefault();
    	$('#showStock').load('php/getinvent.php');
    	$('livestock').animate({scrollTop: 0}, 360);
    });

    $('#ExitLogForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'php/filterexitlog.php',
            type: 'POST',
            dataType: 'HTML',
            data: $('#ExitLogForm').serialize(),

            success: function(response, textStatus, jqXHR){
                $('#showStock').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log('error(s):'+textStatus, errorThrown);
            }

        });
        $('#modalExitLog').modal('toggle');
        $('body').animate({ scrollTop: 0 }, 360);
    });

    $('#EnterLogForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'php/filterEnterLog.php',
            type: 'POST',
            dataType: 'HTML',
            data: $('#EnterLogForm').serialize(),

            success: function(response, textStatus, jqXHR){
                $('#showStock').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log('error(s):'+textStatus, errorThrown);
            }

        });
        $('#modalEnterLog').modal('toggle');
        $('livestock').animate({ scrollTop: 0 }, 360);
    });

    $('#StockPDFEmail').on('click', function(e){
        e.preventDefault();
        /*
        $.ajax({
            url: 'pdf/emailStock.php',
            type: 'POST',
            data: $('#pdfRequestForm').serialize(),

            success: function(response, textStatus, jqXHR){
                console.log(textStatus);
            },

            error: function(jqXHR, textStatus, errorThrown){
                console.log('error(s):'+textStatus, errorThrown);
            }
        })
        */
        document.getElementById("#pdfRequestForm");
        console.log("form submit");
    });

    $('#EmptyPDFEmail').on('click', function(e){
        e.preventDefault();
        $.ajax({
            url: 'php/emailEmpty.php',
            type: 'POST',
            data: $('#pdfRequestForm').serialize(),

            success: function(response, textStatus, jqXHR){
                console.log(textStatus);
            },

            error: function(jqXHR, textStatus, errorThrown){
                console.log('error(s):'+textStatus, errorThrown);
            }
        })
    });


	$(".prodselect").select2({
		tags: true,
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            }
        },
        templateResult: function (data) {
            var $result = $("<span></span>");

            $result.text(data.text);

            if (data.newOption) {
                $result.append(" <em>(new)</em>");
            }

            return $result;
        },
		width: "100%"
	});

    $(".unitselect").select2({
        tags: true,
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            }
        },
        templateResult: function (data) {
            var $result = $("<span></span>");

            $result.text(data.text);

            if (data.newOption) {
                $result.append(" <em>(Registro Nuevo)</em>");
            }

            return $result;
        },
        width: "100%"
    });


});
