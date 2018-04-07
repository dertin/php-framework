function isEmpty(val)
{

    // test results
    //---------------
    // []        true, empty array
    // {}        true, empty object
    // null      true
    // undefined true
    // ""        true, empty string
    // ''        true, empty string
    // 0         false, number
    // true      false, boolean
    // false     false, boolean
    // Date      false
    // function  false

    if (val === undefined){
      return true;
    }

    if (typeof (val) == 'function' || typeof (val) == 'number' || typeof (val) == 'boolean' || Object.prototype.toString.call(val) === '[object Date]'){
      return false;
    }

    if (val == null || val.length === 0){
        return true;  // null or 0 length array
    }

    if (typeof (val) == "object") {
        // empty object

        var r = true;

        for (var f in val){
            r = false;
        }
        return r;
    }

    return false;
}

var AjaxLib = (function() {

    function _serializeParams(params) {
        return Object.keys(params).map(function(key) {
                return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
            })
            .join('&');
    }

    function _makeRequest(sMethod, sUrl, aParams, seralizeNeed,fileOnData) {
        return new Promise(function(resolve, reject) {

            var http_request = false;
            var params = null;
            var promesaRetorno = null;

            if (!sMethod || !sUrl || !aParams) {
                reject('AjaxLib.Err.Params');
                return false;
            }

            if (window.XMLHttpRequest) { // Mozilla, Safari,...
                http_request = new XMLHttpRequest();
                if (http_request.overrideMimeType) {
                    http_request.overrideMimeType('text/xml');
                }
            } else if (window.ActiveXObject) { // IE
                try {
                    http_request = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try {
                        http_request = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e) {}
                }
            }

            if (!http_request) {
                reject('AjaxLib.Err.XMLHttpRequest');
                return false;
            }

            var stateChange = function(xhr) {
                request = xhr.target;
                if (request.readyState !== 4) {
                    return;
                }
                if ([200, 304].indexOf(request.status) === -1) {
                    console.log('AjaxLib.Err.NotFound');
                    reject(request);
                    return false;
                } else {
                    resolve(request.response);
                    return true;
                }
            };

            http_request.onreadystatechange = stateChange;

            if (sMethod == 'POST') {
                http_request.open(sMethod, sUrl, true);
                if (!fileOnData) {
                  //si no se esta pasando un file como archivo,hay que setear header
                  http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                }
                if (seralizeNeed) {
                  //si es necesario serializar los parametros
                  params = _serializeParams(aParams);
                }
                else {
                  params = aParams;
                }
                http_request.send(params);
            } else {
                if (seralizeNeed) {
                  params = _serializeParams(aParams);
                }
                else {
                  params = aParams;
                }
                http_request.open('GET', sUrl + '?' + params, true);
                http_request.send(null);
            }

        });
    }

    function get(url, data, seralizeNeed,fileOnData) {
        return _makeRequest('GET', url, data, seralizeNeed,fileOnData);
    }

    function post(url, data, seralizeNeed,fileOnData) {
        return _makeRequest('POST', url, data, seralizeNeed,fileOnData);
    }

    function validateJSON(jsonData) {
        if (isEmpty(jsonData)){
          return false;
        }
        if (/^[\],:{}\s]*$/.test(jsonData.replace(/\\["\\\/bfnrtu]/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {
            return true;
        } else {
            return false;
        }
    }

    function isFunction(functionToCheck) {
        var getType = {};
        return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
    }

    /* Public API */
    var api = {
        post: post,
        get: get,
        validateJSON: validateJSON,
        isFunction: isFunction
    };

    return api;

})();

var ComboLib = (function() {

    function _initLoad(idNameComboCarga, urlLoad, aNamesParams, funcAfterLoad) {
        var aParams = {};

        if (!isEmpty(aNamesParams)) {
          aNamesParams.forEach(function(elemParamIdName) {
              var elemParamValue = document.getElementById(elemParamIdName).value;

              if (typeof elemParamValue != 'undefined') {
                  aParams[elemParamIdName] = elemParamValue;
              }
          });
        }

        return loadCombo(idNameComboCarga, urlLoad, aParams, funcAfterLoad);

    }


    function eventComboChange(idNameComboCambio, idNameComboCarga, urlLoad, aNamesParams, noRequiereEvento, funcAfterLoad) {

        document.getElementById(idNameComboCambio).addEventListener("change", function() {
            _initLoad(idNameComboCarga, urlLoad, aNamesParams, funcAfterLoad);
        });

        if (typeof noRequiereEvento != 'undefined' && noRequiereEvento == true) {
            _initLoad(idNameComboCarga, urlLoad, aNamesParams, funcAfterLoad);
        }


    }


    function loadCombo(idNameComboBox, urlLoad, aParams, funcAfterLoad) {
        var elemComboBox = document.getElementById(idNameComboBox);

        elemComboBox.innerHTML = ""; //vacio comboBox

        //ejecuta consulta AJAX
        var promise = AjaxLib.post(urlLoad, aParams,true,false);

        promise.then(function(response) {

            //validar formato JSON
            if (!AjaxLib.validateJSON(response)) {
                return false;
            }

            //convertir JSON en array
            var jsonData = JSON.parse(response);

            if (isEmpty(jsonData))
            {
              return false;
            }

            //cargar comboBox con options
            jsonData.forEach(function(option) {
                _appendOption(elemComboBox, option.name, option.value);
            });

            if (typeof funcAfterLoad != 'undefined' && AjaxLib.isFunction(funcAfterLoad)) {
                return funcAfterLoad();
            }

            return true;

        }).catch(function(reason) {
            console.log('ComboLib.Err.AjaxLoad');
            return false;
        });

    }

    function _appendOption(elemComboBox, textOption, valueOption) {
        var option = document.createElement("option");
        option.text = textOption;
        option.value = valueOption;
        elemComboBox.appendChild(option);
    }


    /* Public API */
    var api = {
        eventComboChange: eventComboChange,
        loadCombo: loadCombo
    };

    return api;

})();

var TableLib = (function() {

    var jsonData = null;
    var tableName = null;
    var aActionsButton = null;
    var orderState = true;
    var sizeP = false;

    var _eventPagination = function (event)
    {

      var elemTarget = event.target || event.srcElement;
      if ((elemTarget.className !== 'btn btn-default navPagination')) {
        return false;
      }
      if(jsonData == null || aActionsButton == null){
          return false;
      }

      /*obtenemos los datos de la fila con la categoria a procesar y la funcion a aplicar*/
      var numPage = elemTarget.getAttribute('data-page');
      changePage(numPage);

    };

    var _eventAccionButton = function (event)
    {

      var elemTarget = event.target || event.srcElement;
      if ((elemTarget.className !== 'btn btn-default jsActionTable')) {
        return false;
      }
      if(jsonData == null || aActionsButton == null){
          return false;
      }

      /*obtenemos los datos de la fila con la categoria a procesar y la funcion a aplicar*/
      var numRow = elemTarget.getAttribute('data-row');
      var keyAction = elemTarget.getAttribute('data-keyAction');
      var funcAction = aActionsButton[keyAction].action;
      var aInfoRow = jsonData.tbody[numRow];
      funcAction(aInfoRow,numRow);

    };

    var _eventOrderColumn = function (event)
    {

      var elemTarget = event.target || event.srcElement;
      if ((elemTarget.parentElement.className !== 'jsOrderColumn') || (elemTarget.tagName !== 'TH')) {
        return false;
      }
      if(jsonData == null){
          return false;
      }
      var orderByColumn = elemTarget.getAttribute('data-column');
      if (!orderByColumn){
        return false;
      }
      orderState = orderState == false ? true : false;

      function orderElmsColumn(columnName,orderStateIn){
        return function (a,b){
        	var result = 0;
          if(a[columnName] < b[columnName]){result = -1;}
          if(a[columnName] > b[columnName]){result = 1;}
          if (!orderStateIn && result !== 0) {
          	result = result * -1;
          }
          return result;
        };
      }
      jsonData.tbody.sort(orderElmsColumn(orderByColumn,orderState));

      _createTable(jsonData);

      // TODO: falta analizar si ejecutar las funcion funcAfterLoad

    };

    function _createTable(jsonData,init,size)
    {
        var elemHeadTh = null;
        var elemHeadTr = null;
        var elemBodyTr = null;
        var elemBodyTd = null;
        var text = null;
        var numColumn = 0;

        document.getElementById(tableName).innerHTML = "";
        var tbl = document.getElementById(tableName);

        /* CREAR THEAD DE LA TABLA */
        var thead = document.createElement('thead');
        var aPosColumn = [];
        elemHeadTr = document.createElement('tr');
        elemHeadTr.className = 'jsOrderColumn';

        if (isEmpty(Object.keys(jsonData.thead))){
          return false;
        }

        Object.keys(jsonData.thead).forEach(function(keyColumn,indexColumn) {
            var itemColumn = jsonData.thead[keyColumn];
            if(itemColumn.visibilidad == 1){
              elemHeadTh = document.createElement('th');
              elemHeadTh.setAttribute('data-column',keyColumn);
              text = document.createTextNode(itemColumn.nameColumn);
              elemHeadTh.appendChild(text);
              elemHeadTr.appendChild(elemHeadTh);
              aPosColumn[itemColumn.orden] = keyColumn;
            }
        });

        if (aActionsButton.length > 0)
        {
          elemHeadTh = document.createElement('th');
          text = document.createTextNode('Acciones');
          elemHeadTh.appendChild(text);
          elemHeadTr.appendChild(elemHeadTh);
        }


        thead.appendChild(elemHeadTr);
        tbl.appendChild(thead);

        /* CREAR TBODY DE LA TABLA */
        var tbody = document.createElement('tbody');

        /* Armar filas */
        for (var i = init; i < size; i++) {

            elemBodyTr = document.createElement('tr');
            var idTr = "jsTableLibTr" + i;
            elemBodyTr.setAttribute('id',idTr);
            numColumn = Object.keys(jsonData.tbody[i]).length;
            /* Armar columnas */
            for (var j = 1; j <= numColumn; j++) {
              if(typeof aPosColumn[j] == 'undefined'){
                continue;
              }

              keyColumn = aPosColumn[j];
              var sColumnText = jsonData.tbody[i][keyColumn];

              elemBodyTd = document.createElement('td');
              text = document.createTextNode(sColumnText);

              elemBodyTd.appendChild(text);
              elemBodyTr.appendChild(elemBodyTd);
            }

            /*se crean los botones para las acciones del arreglo,
            deberia verificarse que categorias pueden realizar que acciones*/
            if (aActionsButton.length > 0)
            {
              elemBodyTd = document.createElement('td');
              var divButtonGroup = document.createElement('div');
              divButtonGroup.className = 'btn-group';

              for (var keyAction in aActionsButton){
                var itemActionButton = aActionsButton[keyAction];
                var elemActionButton = document.createElement('button');
                elemActionButton.className = 'btn btn-default jsActionTable';
                elemActionButton.type = 'button';
                elemActionButton.setAttribute('data-row',i);
                elemActionButton.setAttribute('data-keyAction',keyAction);
                var elemTextAction = document.createTextNode(itemActionButton.name);
                elemActionButton.appendChild(elemTextAction);
                divButtonGroup.appendChild(elemActionButton);
              }

              elemBodyTd.appendChild(divButtonGroup);
              elemBodyTr.appendChild(elemBodyTd);
            }
            tbody.appendChild(elemBodyTr);
        }
        tbl.appendChild(tbody);
    }

    function _loadTable(urlLoad, aParams, funcAfterLoad)
    {
        /* Ejecuta consulta AJAX */
        var promise = AjaxLib.post(urlLoad, aParams, true, false);

        promise.then(function(response) {
            var realSize = 0;
            //validar formato JSON
            if (!AjaxLib.validateJSON(response)) {
                return false;
            }

            //convertir JSON en array
            jsonData = JSON.parse(response);

            if ((sizeP != false) && jsonData.tbody.length > sizeP) {
              _createNavPagination(jsonData.tbody.length);
              realSize = _adjustSizePage(1);
            }
            else {
              realSize = jsonData.tbody.length;
              _deleteNavPagination();
            }


            _createTable(jsonData,0,realSize);

            document.removeEventListener('click',_eventAccionButton);
            document.removeEventListener('click',_eventOrderColumn);
            document.addEventListener('click',_eventOrderColumn);
            document.addEventListener('click',_eventAccionButton);



            if (typeof funcAfterLoad != 'undefined' && AjaxLib.isFunction(funcAfterLoad)) {
                return funcAfterLoad();
            }

            return true;

        }).catch(function(reason) {
            console.log('ComboLib.Err.AjaxLoad');
            return false;
        });

    }

    function init(tableNameIn ,urlLoad, aNamesParams, aActionsButtonIn, sizePage ,funcAfterLoad)
    {
        orderState = true;
        tableName = tableNameIn;
        aActionsButton = aActionsButtonIn;
        if (sizePage != 0) {
          sizeP = sizePage;
        }

        var aParams = {};
        /*document.removeEventListener('click',_eventAccionButton);
        document.removeEventListener('click',_eventOrderColumn);
        document.addEventListener('click',_eventOrderColumn);*/

        //para cada nombre de parametro del arreglo se obtine su valor
        if (!isEmpty(aNamesParams)) {
          aNamesParams.forEach(function(elemParamIdName) {
              var elemParamValue = document.getElementById(elemParamIdName).value;
              if (typeof elemParamValue != 'undefined') {
                  aParams[elemParamIdName] = elemParamValue;
              }
          });
        }
        return _loadTable(urlLoad, aParams, funcAfterLoad);

    }

    function _adjustSizePage(nextPage)
    {
      var realSize= 0;
      if (jsonData.tbody.length < sizeP*nextPage) {
        realSize = jsonData.tbody.length;
      }
      else {
        realSize = sizeP*nextPage;
      }
      return realSize;
    }

    function changePage(nextPage)
    {
      var realSize = _adjustSizePage(nextPage);
      _createTable(jsonData,(nextPage-1)*sizeP,realSize);
      _changePrevAndNext(nextPage);
    }

    function _createNavPagination(cantData)
    {

      var container = document.getElementsByClassName('container-fluid')[1];
      var divRow = document.createElement('div');
      divRow.className = 'row';
      divRow.id = 'jsTableLibRowPagination';
      var divNav = document.createElement('div');
      divNav.className = 'divNav';

      var nav = document.createElement('nav');
      //var text = document.createTextNode('Page navigation example');
      //nav.setAttribute('arial-label',text);

      var ulNav = document.createElement('ul');
      ulNav.className = 'pagination';

      //create previous button
      var liPrevNav = document.createElement('li');
      var buttonPrevLiNav = document.createElement('button');
      buttonPrevLiNav.className = 'btn btn-default navPagination';
      buttonPrevLiNav.type = 'button';
      buttonPrevLiNav.id = 'prevButtonNav';
      buttonPrevLiNav.setAttribute('data-page',1);
      var span1ButtonPrevLiNav = document.createElement('span');
      span1ButtonPrevLiNav.className = 'glyphicon glyphicon-menu-left';
      buttonPrevLiNav.appendChild(span1ButtonPrevLiNav);
      liPrevNav.appendChild(buttonPrevLiNav);

      //append previous to nav bar
      ulNav.appendChild(liPrevNav);

      var totalPages = cantData / sizeP;
      for (var i = 0; i < totalPages; i++) {
        var liNav = document.createElement('li');
        var buttonLiNav = document.createElement('button');
        buttonLiNav.className = 'btn btn-default navPagination';
        buttonLiNav.type = 'button';
        buttonLiNav.setAttribute('data-page',i+1);
        var textButton = document.createTextNode(i + 1);
        buttonLiNav.appendChild(textButton);
        liNav.appendChild(buttonLiNav);
        ulNav.appendChild(liNav);
      }

      //crate next button
      var liNextNav = document.createElement('li');
      var buttonNextLiNav = document.createElement('button');
      buttonNextLiNav.className = 'btn btn-default navPagination';
      buttonNextLiNav.type = 'button';
      buttonNextLiNav.id = 'nextButtonNav';
      buttonNextLiNav.setAttribute('data-page',2);
      var span1ButtonNextLiNav = document.createElement('span');
      span1ButtonNextLiNav.className = 'glyphicon glyphicon-menu-right';
      buttonNextLiNav.appendChild(span1ButtonNextLiNav);
      liNextNav.appendChild(buttonNextLiNav);

      //append next to navBar
      ulNav.appendChild(liNextNav);

      nav.appendChild(ulNav);
      divNav.appendChild(nav);
      divRow.appendChild(divNav);
      container.appendChild(divRow);

      document.removeEventListener('click',_eventPagination);
      document.addEventListener('click',_eventPagination);

    }

    function _deleteNavPagination()
    {
      var divRow = document.getElementById('jsTableLibRowPagination');
      if (divRow != null){
        divRow.innerHTML="";
      }
    }

    function _changePrevAndNext(nextPage)
    {
      var divRow = document.getElementById('jsTableLibRowPagination');
      if (divRow != null){
        var nextButtonNav = document.getElementById('nextButtonNav');
        var prevButtonNav = document.getElementById('prevButtonNav');
        var valueNextPage = parseInt(nextPage);
        switch (valueNextPage) {
          case 1:
            prevButtonNav.removeAttribute('data-page');
            nextButtonNav.removeAttribute('data-page');
            prevButtonNav.setAttribute('data-page',1);
            nextButtonNav.setAttribute('data-page',2);
          break;

          default:
            var totalPagesPagination = document.getElementsByClassName('btn btn-default navPagination');
            if (nextPage < totalPagesPagination.length - 2) {
              prevButtonNav.removeAttribute('data-page');
              nextButtonNav.removeAttribute('data-page');
              prevButtonNav.setAttribute('data-page',valueNextPage-1);
              nextButtonNav.setAttribute('data-page',valueNextPage+1);
            }
            else {
              prevButtonNav.removeAttribute('data-page');
              prevButtonNav.setAttribute('data-page',valueNextPage-1);
            }
        }
      }
    }



    /* Public API */
    var api = {
        init : init,
        changePage : changePage
    };


    return api;

})();
