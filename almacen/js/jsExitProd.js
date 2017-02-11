function exitProdGrid(){
    (function(jsGrid, $, undefined) {
        var NumberField = jsGrid.NumberField;

        function Select2Field(config) {
            this.items = [];
            this.selectedIndex = -1;
            this.valueField = "";
            this.textField = "";
            this.imgField = "";

            if (config.valueField && config.items.length)
            this.valueType = typeof config.items[0][config.valueField];
            this.sorter = this.valueType;
            NumberField.call(this, config);
        }

        Select2Field.prototype = new NumberField({
            align: "left",
            valueType: "number",

            itemTemplate: function(value) {
                var items = this.items,
                valueField = this.valueField,
                textField = this.textField,
                imgField = this.imgField,
                resultItem;

                if(valueField) {
                    resultItem = $.grep(items, function(item, index) {
                        return item[valueField] === value;
                    })[0] || {};
                }
                else
                    resultItem = items[value];

                var result = (textField ? resultItem[textField] : resultItem);
                
                return (result === undefined || result === null) ? "" : result;
            },

            filterTemplate: function() {
                if(!this.filtering)
                    return "";

                var grid = this._grid,
                $result = this.filterControl = this._createSelect();
                this._applySelect($result, this);

                if(this.autosearch) {
                    $result.on("change", function(e) {
                    grid.search();
                    });
                }

                return $result;
            },

            insertTemplate: function() {
            if(!this.inserting)
                return "";

            var $result = this.insertControl = this._createSelect();
            this._applySelect($result, this);
            return $result;
            },

            editTemplate: function(value) {
                if(!this.editing)
                    return this.itemTemplate(value);

                var $result = this.editControl = this._createSelect();
                (value !== undefined) && $result.val(value);
                this._applySelect($result, this);
                return $result;
            },

            filterValue: function() {
                var val = this.filterControl.val();
                return this.valueType === "number" ? parseInt(val || 0, 10) : val;
            },

            insertValue: function() {
                var val = this.insertControl.val();
                return this.valueType === "number" ? parseInt(val || 0, 10) : val;
            },
            editValue: function() {
                var val = this.editControl.val();
                return this.valueType === "number" ? parseInt(val || 0, 10) : val;
            },

            _applySelect: function(item, self)
            {
                setTimeout(function() {
                    var selectSiteIcon = function(opt){
                        var img = '';
                        try {
                            img = opt.element.attributes.img.value;
                        } catch(e) {}
                        if (!opt.id || !img)
                         return opt.text;
                        var res = $('<span><img src="' + img + '" class="img-flag"/> ' + opt.text + '</span>');
                        return res;
                    }
                    item.select2({
                        templateResult: selectSiteIcon,
                        templateSelection: selectSiteIcon
                    });
                });
            },

            _createSelect: function() {
                var $result = $("<select>"),
                valueField = this.valueField,
                textField = this.textField,
                imgField = this.imgField,
                selectedIndex = this.selectedIndex;

                $.each(this.items, function(index, item) {
                    var value = valueField ? item[valueField] : index,
                        text = textField ? item[textField] : item,
                        img = imgField ? item[imgField] : '';

                var $option = $("<option>")
                    .attr("value", value)
                    .attr("img", img)
                    .text(text)
                    .appendTo($result);

                    $option.prop("selected", (selectedIndex === index));
                });
                return $result;
            }
        });
        jsGrid.fields.select2 = jsGrid.Select2Field = Select2Field;

    }(jsGrid, jQuery));


    

    /*end custom fields*/

    $.ajax({
        type: "GET",
        url: "php/prodcall.php",
        dataType: "json",
    }).done(function(consume){

        var units = [
            {ID: "1", Unit: "Piezas"},
            {ID: "3", Unit: "Tanques"},
            {ID: "4", Unit: "Cajas"},
            {ID: "5", Unit: "Litros"},
            {ID: "6", Unit: "Pliegos"},
        ];

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


    $("#jsExitProd").jsGrid({
        height: "auto",
        width: "100%",
 
        inserting: true,
        editing: true,
        sorting: true,
        paging: true,
        autoload: true,

        noDataContent: "Registrar Consumibles Entregados",
        invalidMessage: "[ERROR] Corregir los siguientes errores: ",

        pageSize: 10,
        pageButtonCount: 5,

        deleteConfirm: "¿Está seguro de querer eliminar esta linea?",



        fields: [
            
            {
                name: "LogProduct",
                title: "Producto",
                type: "select2",
                width: 110,
                items: consume,
                textField: "CommonName",
                valueField: "ID",

                validate: [
                    {validator: "required", message: "No se ha ingresado un Producto para la Partida"}
                ]
            },
            {
                name: "NumMoved",
                title: "Cantidad",
                type: "number",
                width: 55,
                validate: [
                    {validator: "required", message: "No se ha ingresado una Cantidad"},
                    {validator: "min", message: "Cantidad no puede ser menor a 1", param:[1]}
                ]
            },
            {
                name: "Unit",
                title: "",
                type: "select",
                width: 50,
                items: units,
                textField: "Unit",
                valueField: "ID",
            },
            {
                name: "WhoRequest",
                title: "Solicitante",
                type: "text",
                width: 75,
                validate: [
                    {validator: "required", message: "No se ha ingresado la persona que Solicita el Material"},
                ]
            },
            {
                name: "WhoArea",
                title: "Departamento",
                type: "select2",
                width: 100,
                items: areas,
                textField: "AreaName",
                valueField: "ID",
            },
            {
                type: "control",
                modeSwitchButton: false,
                headerTemplate: function() {
                    return $("<button>").attr("type", "button").attr("class", "btn btn-primary").text("Guardar")
                            .on("click", function () {
                                var items = $("#jsExitProd").jsGrid("option", "data");
                                sendToFile(items);
                            });
                }
            }
        ]

        });
    });


    var sendToFile = function(){
        var items = $("#jsExitProd").jsGrid("option", "data");
        
        var str = JSON.stringify(items);

        $.ajax({
            url: "php/postnewprods.php",
            type: "POST", 
            data: {'exitprod': str},

            success: function(data){
                if(data==1){

                    $div = $('<div id="yayDialog" title="EXITO">');
                    $div.append('<p><b>Los Productos han sido ingresados con éxito.</b></p>');

                    $div.dialog({
                        modal: true,
                        maxHeight:500,
                    }).prev(".ui-dialog-titlebar").css("background", "green");

                    $('#showStock').load('php/getinvent.php');
                    $("#jsExitProd").jsGrid("destroy");
                    exitProdGrid();
                }
                else{
                    if(data==2){
                        $div = $('<div id="welpDialog" title="ATENCION"></div>');
                        $div.append('<p>Orden ingresada con éxito.<br>Tomar en cuenta que uno de los productos está <u>cerca de agotarse</u>');

                        $div.dialog({
                            modal: true,
                            maxHeight: 500,
                        }).prev(".ui-dialog-titlebar").css("background", "yellow");

                        $('#showStock').load('php/getinvent.php');
                        $("#jsExitProd").jsGrid("destroy");
                        exitProdGrid();

                    }
                    else{

                    $div = $('<div id="errorDialog" title="ERROR">');
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
    }
    
}
