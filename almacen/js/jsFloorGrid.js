function floorToolsGrid(){

    var areas = [
            
            {ID: "11", AreaName: "Soldadura"},
            {ID: "13", AreaName: "Pulido"},
            {ID: "8", AreaName: "Pintura"},
            {ID: "6", AreaName: "Maquinados"},
            {ID: "12", AreaName: "Ensamble"},
            {ID: "9", AreaName: "Troquelado"},
            {ID: "10", AreaName: "Doblado"},
            {ID: "2", AreaName: "Development"},

        ]

    $("#jsFloorGrid").jsGrid({
        height: "auto",
        width: "100%",

        filtering: true,
        editing: true,
        paging: true,
        sorting: true,
        autoload: true,
        paging: true,

        noDataContent: "Todas las herramientas se encuentran disponibles en el almacén.",

        pageSize: 15,
        pageButtonCount: 5,


        deleteConfirm: "¿Está seguro de querer eliminar esta linea?",

        rowClick: function(args) {
            showEndLoanDialog("Edit", args.item);
        },

        controller: {
            loadData: function (filter) {
                var d = $.Deferred();

                $.ajax({
                    type: 'GET',
                    url: 'php/loancall.php',
                    dataType: 'json'
                }).done(function (result) {
                    
                    result = $.grep(result, function(item){
                        return (!filter.ToolName || item.ToolName.toLowerCase().indexOf(filter.ToolName.toLowerCase()) > -1)
                                && (!filter.ToolLive || item.ToolLive.toLowerCase().indexOf(filter.ToolLive.toLowerCase()) > -1)
                                && (!filter.HasTool || item.HasTool.toLowerCase().indexOf(filter.HasTool.toLowerCase()) > -1)
                                && (!filter.AreaName || item.AreaName.toLowerCase().indexOf(filter.AreaName.toLowerCase()) > -1)
                                && (!filter.ToolStock || item.ToolStock.toLowerCase().indexOf(filter.ToolStock.toLowerCase()) > -1)
                                && (!filter.ToolTime || item.ToolTime.toLowerCase().indexOf(filter.ToolTime.toLowerCase()) > -1);
                    });

                    d.resolve(result);
                })
                return d.promise();
            }
        },

        fields: [
            { name: "ToolHash", title: "Codigo", type: "text", css: "hide"},
            { name: "ToolName", title: "Herramienta", type: "text", width: 75 },
            { name: "ToolLive", title: "Cantidad Regreso", type: "number", width: 80 },
            { name: "HasTool", title: "En Uso", type: "text", width: 60 },
            { name: "ToolStock", title: "Total Piezas Almacén", type: "number", width: 90 },
            { name: "AreaName", title: "Departamento", type:"text", width: 80},
            { name: "ToolTime", title: "Hora de Salida", type: "text", width: 90 },
            {
                type: "control",
                editButton: false,
                deleteButton: false,
            }
        ]
    });

    $("#endLoanDialog").dialog({
        autoOpen: false,
        width: 450,
        close: function() {
            $("#endLoanForm").validate().resetForm();
            $("#endLoanForm").find(".error").removeClass("error");
        }
    });

    $("#endLoanForm").validate({
        rules: {
            editToolHash: "required",
            editToolName: "required",
            editToolLive: "required",
            editHasTool: "required",
            editToolStock: "required",
            editAreaName: "required",
            editToolTime: "required",
            editProgress: "required",
        },
        messages: {
            editToolHash: "this is required",
            editToolName: "this is required",
            editToolLive: "this is required",
            editHasTool: "this is required",
            editToolStock: "this is required",
            editAreaName: "this is required",
            editToolTime: "this is required",
        },
    });

    var submitHandler = $.noop;

    $("#endLoanDialog").find("form").submit(function(e) {
        e.preventDefault();
        submitHandler();
    });

    var showEndLoanDialog = function(dialogType, client) {
        $("#editToolHash").val(client.ToolHash);
        $("#editToolName").val(client.ToolName);
        $("#editToolLive").val(client.ToolLive);
        $("#editHasTool").val(client.HasTool);
        $("#editToolStock").val(client.ToolStock);
        $("#editAreaName").val(client.AreaName);
        $("#editToolTime").val(client.ToolTime);

        submitHandler = function(event) {
            saveClient(client, dialogType === "Add");

        };

        $("#endLoanDialog").dialog("option", "title", "Recibo de Producto")
                .dialog("open");
    };

    var saveClient = function(client, isNew) {
        $.extend(client, {
            ToolHash: $("#editToolHash").val(),
            ToolName: $("#editToolName").val(),
            ToolLive: parseInt($("#editToolLive").val(), 10),
            HasTool: $("#editHasTool").val(),
            ToolStock: parseInt($("#editToolStock").val(), 10),
            AreaName: $("#editAreaName").val(),
            ToolTime: $("#editToolTime").val()
        });


            var str = JSON.stringify(client);

            $.ajax({
            url: "php/postgridtools.php",
            type: "POST", 
            data: {'toolback': str},

            success: function(data){
                if(data==1){

                    $div = $('<div id="floorSuccess" title="EXITO"></div>');
                    $div.append('<p><b>La herramienta ha sido registrada correctamente.</b></p>');

                    $div.dialog({
                        modal: true,
                        maxHeight:500,
                        open: function(event, ui) {
                            setTimeout(function(){
                                div.dialog('close');                
                            }, 3000);
                        }
                    }).prev(".ui-dialog-titlebar").css("background", "green");

                    $("#jsExitToolGrid").jsGrid("destroy");
                    exitToolGrid();
                    $("#jsFloorGrid").jsGrid("destroy");
                    floorToolsGrid();
                }
                else{

                    if(data==2){
                        $("#jsExitToolGrid").jsGrid("destroy");
                        exitToolGrid();
                        $("#jsFloorGrid").jsGrid("destroy");
                        floorToolsGrid();
                    }
                    else{

                        $div = $('<div id="floorError" title="ERROR"></div>');
                        $div.append('<p><b>'+data+'</b></p>');

                        $div.dialog({
                            modal: true,
                            maxHeight:500,
                        }).prev(".ui-dialog-titlebar").css("background", "red");

                        console.log(data);
                    }
                    
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert("Algo pasó. Informar al encargado de Desarrollo [Something Happened. Console for Details]");
                console.log('error(s): '+textStatus, errorThrown);
            }
        });
        

        $("#jsFloorGrid").jsGrid(isNew ? "insertItem" : "updateItem", client);

        $("#endLoanDialog").dialog("close");
    };

}