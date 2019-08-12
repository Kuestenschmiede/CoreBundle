/**
 * con4gis - the gis-kit
 *
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */
export class AutocompleteHandler {
    constructor(inputField, objFunctions = {}, cssId, objSettings = {}, containerAddresses = {}) {
        this.inputField = inputField;
        this.objFunctions = objFunctions;
        this.cssId = cssId;
        this.objSettings = objSettings;
        this.containerAddresses = containerAddresses;
    }
    setCenter (center) {
        this.objSettings.center = center;
    }
    handleInput () {
        const scope = this;
        this.inputField.autocomplete({
            source: this.containerAddresses.arrNames
        });
        let enterListener = function(event, opt_this) {
            //const scope = this;
            if (event.keyCode === 13) {
                opt_this.objFunctions.submitFunction(opt_this, event.currentTarget.classList[0]);
            } else if (event.keyCode === 8 || (event.keyCode >= 37 && event.keyCode <= 40) || event.keyCode === 9) {
                // event.stopPropagation();
                // event.preventDefault();
            } else {
                if ($(event.currentTarget).val().length == 0 && !event.keyCode) { //deleted
                    this.objFunctions.deleteFunction(event.currentTarget, event.currentTarget.classList[0]);

                    let cssClass = event.currentTarget.classList[0];
                    if (cssClass.indexOf('from') != -1) {
                        travelData.routeFrom = {};
                        opt_this.containerAddresses.arrFromPositions = [];
                        opt_this.containerAddresses.arrFromPositions = [];
                    }
                    else if (cssClass.indexOf('to') != -1){
                        travelData.routeTo = {};
                        opt_this.containerAddresses.arrToNames = [];
                        opt_this.containerAddresses.arrToPositions = [];
                    }
                    else{
                        console.log("This is weird");
                    }
                }
                else {
                    let currTime = Math.floor(Date.now());
                    opt_this.counter = currTime;
                    setTimeout(function() {
                        if (opt_this.counter && opt_this.counter + 400 < Math.floor(Date.now())) {
                            delete opt_this.counter;
                            opt_this.autocompleteAddress($(event.currentTarget).val(), "." + event.currentTarget.classList[0]);
                        }
                    },500);
                }
            }
        };
        this.inputField.on('keydown', (event) => enterListener(event, scope));
        this.inputField.on('search', enterListener)
        this.inputField.on('autocompleteselect', this.objFunctions.selectListener);
        this.inputField.on('change', this.objFunctions.changeListener);
    }
    autocompleteAddress(input, cssClass) {
        const scope = this;
        let center;
        if (scope.objSettings.center) { 
            if (typeof scope.objSettings.center === "function") {
                let objCenter = scope.objSettings.center();
                center = objCenter[0] + "," + objCenter[1];
            }
            else {
                center = scope.objSettings.center[0] + "," + scope.objSettings.center[1];    
            }
        }
        else if(scope.objSettings.bBox){
            center = (parseFloat(scope.objSettings.bBox[0]) + parseFloat(scope.objSettings.bBox[2])) / 2 + "," + (parseFloat(scope.objSettings.bBox[1]) + parseFloat(scope.objSettings.bBox[3])) / 2;
        }
        let url;
        if (center) {
            url = scope.objSettings.proxyUrl + "autocomplete.php?format=json&key=" + scope.objSettings.keyAutocomplete + "&q=" + input +"&center=" + center;
        }
        else {
            url = scope.objSettings.proxyUrl + "autocomplete.php?format=json&key=" + scope.objSettings.keyAutocomplete + "&q=" + input;
        }
        if (this.objSettings.autoLength) {
            url += "&limit=" + this.objSettings.autoLength;
        }
        $.ajax({url: url}).done(function(data) {
            let center;
            if (scope.objSettings.center) {
                center = scope.objSettings.center;
            }
            else if (scope.objSettings.bBox){
                center = [(parseFloat(scope.objSettings.bBox[0]) + parseFloat(scope.objSettings.bBox[2])) / 2, (parseFloat(scope.objSettings.bBox[1]) + parseFloat(scope.objSettings.bBox[3])) / 2];
            }
            if (data.length > 0) {

                if (data[0] && data[0].display_name  && center) {
                    // $(cssId).val(data[0].display_name);
                    let arrAddresses = [];
                    for (let i in data) {
                        if (data.hasOwnProperty(i)) {
                            if (scope.objSettings.bBox && scope.objSettings.bBox[0]) {
                                if (scope.isInBoundingBox(data[i].lon, data[i].lat, scope.objSettings.bBox)) {
                                    let distance = Math.sqrt((center[0] - data[i].lon) * (center[0] - data[i].lon) + (center[1] - data[i].lat) * (center[1] - data[i].lat));
                                    let element = {
                                        'dist' : distance,
                                        'pos'  : [data[i].lat, data[i].lon],
                                        'name' : data[i].display_name
                                    };
                                    arrAddresses.push(element);
                                }
                            }
                        }
                    }
                    arrAddresses.sort((a,b) => a.dist -b.dist);

                    for (let i in arrAddresses) {
                        if (arrAddresses.hasOwnProperty(i)) {
                            if (cssClass.indexOf('from') != -1) {
                                // do not add twice
                                if (!scope.containerAddresses.arrFromNames.includes(arrAddresses[i].name)) {
                                    scope.containerAddresses.arrFromNames.push(arrAddresses[i].name);
                                    scope.containerAddresses.arrFromPositions.push(arrAddresses[i].pos);
                                }
                            }
                            else if (cssClass.indexOf('to') != -1){
                                if (!scope.containerAddresses.arrToNames.includes(arrAddresses[i].name)) {
                                    scope.containerAddresses.arrToNames.push(arrAddresses[i].name);
                                    scope.containerAddresses.arrToPositions.push(arrAddresses[i].pos);
                                }
                            }
                            else {
                                console.log("This is weird");
                            }

                        }
                    }
                    // trigger keydown event to show autocomplete options
                    let event = jQuery.Event("keydown", {keyCode: 8});
                    $(cssClass).trigger(event);
                }
                for(let i in data) {
                    if(data.hasOwnProperty(i)) {
                        if (cssClass.indexOf('from') != -1) {
                            // do not add twice
                            if (!scope.containerAddresses.arrFromNames.includes(data[i].display_name)) {
                                scope.containerAddresses.arrFromNames.push(data[i].display_name);
                                scope.containerAddresses.arrFromPositions.push([data[i].lat, data[i].lon]);
                            }
                        }
                        else if (cssClass.indexOf('to') != -1){
                            if (!scope.containerAddresses.arrToNames.includes(data[i].display_name)) {
                                scope.containerAddresses.arrToNames.push(data[i].display_name);
                                scope.containerAddresses.arrToPositions.push([data[i].lat, data[i].lon]);
                            }
                        }
                        else {
                            console.log("This is weird");
                        }
                    }
                }
                // trigger keydown event to show autocomplete options
                let event = jQuery.Event("keydown", {keyCode: 8});
                $(cssClass).trigger(event);
            }
        });
    }
    isInBoundingBox(longitude, latitude, bbox) {
        if (typeof longitude === "string") {
            longitude = parseFloat(longitude);
        }
        if (typeof latitude === "string") {
            latitude = parseFloat(latitude);
        }
        if (bbox[0] < longitude &&
            longitude < bbox[2] &&
            bbox[1] < latitude &&
            latitude < bbox[3]) {
            return true;
        } else {
            return false;
        }
    }
}