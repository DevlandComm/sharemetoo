/*
The following directive is for the file upload control with attribute = file-model.
Since Angular does not support the file upload, we have to create the directive ourselves.
This binds the onchange event so we can capture the file once selected from the file select control
*/
myApp.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function() { 
								modelSetter(scope, element[0].files[0]); 
                                scope.filename = element[0].files[0].name;
							 });
            });
        }
    };
}]);



myApp.directive('scrolly', function() {
    return {
        restrict: 'A',
        link: function(scope,element,attrs) {
            var raw = element[0];
            
            element.bind('scroll', function() {
                if( raw.scrollTop === (raw.scrollHeight - raw.offsetHeight) )
                {
                    scope.$apply(attrs.scrolly);
                }
                
            });
        }
    };
});


/*
// When using this use element: <upload to="server/upload.php" ng-model="object.id"></upload>
myApp.directive('upload', ['$http', function($http) {
    return {
        restrict: 'E',
        replace: true,
        scope: {},
        require: '?ngModel',
        template: '<div class="asset-upload">Drag here to upload</div>',
        link: function(scope, element, attrs, ngModel) {

            // Code goes here
            element.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
            element.on('dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
            
            element.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (e.originalEvent.dataTransfer){
                    if (e.originalEvent.dataTransfer.files.length > 0) {
                        upload(e.originalEvent.dataTransfer.files);
                    }
                }
                return false;
            });

            
            var upload = function(files) {
                var data = new FormData();
                angular.forEach(files, function(value){
                    data.append("files[]", value);
                });

                data.append("objectId", ngModel.$viewValue);

                $http({
                    method: 'POST',
                    url: attrs.to,
                    data: data,
                    withCredentials: true,
                    headers: {'Content-Type': undefined },
                    transformRequest: angular.identity
                }).success(function() {
                    console.log("Uploaded");
                }).error(function() {
                    console.log("Error");
                });
            };
            
        }
    };
}]);
*/


myApp.directive('modalDialog', function() {
    return {
        restrict: 'E',
        scope: {
          show: '='            
        },
        replace: true, // Replace with the template below
        transclude: true, // we want to insert custom content inside the directive
        
        link: function(scope, element, attrs) {
            scope.dialogStyle = {};
            
            
            if (attrs.width)
                scope.dialogStyle.width = attrs.width;
            
            if (attrs.height)
                scope.dialogStyle.height = attrs.height;
				
            scope.hideModal = function() {
                scope.show = false;
            };
            
        },
            
        template: "<div ng-show='show'>" +
                        "<div class='ng-modal-overlay' ng-click='hideModal()'></div>" +
                            "<div class='ng-modal-dialog' ng-style='dialogStyle'>" +
                                "<div class='ng-modal-close' ng-click='hideModal()'>X</div>" +
                                "<div class='ng-modal-dialog-content' ng-transclude></div>" +
                            "</div>" +
                    "</div>"
    };
});




myApp.directive("signatureDir", ['$document', '$log', '$rootScope', function ($document, $log, $rootScope) {
    return {
        restrict: "A",
        link: function (scope, element, attrs) {
            
            var ctx = element[0].getContext('2d');
            // variable that decides if something should be drawn on mousemove
            var drawing = false;

            // the last coordinates before the current move
            var lastX;
            var lastY;
            
            element.bind('mousedown', function(event){
                lastX = event.offsetX;
                lastY = event.offsetY;

                // begins new line
                ctx.beginPath();

                drawing = true;
            });
            element.bind('mousemove', function(event){
                if(drawing){
                  // get current mouse position
                  currentX = event.offsetX;
                  currentY = event.offsetY;

                  draw(lastX, lastY, currentX, currentY);

                  // set current coordinates to last one
                  lastX = currentX;
                  lastY = currentY;
                }

            });
            element.bind('mouseup', function(event){
                // stop drawing
                drawing = false;
            });

            // canvas reset
            function reset(){
                element[0].width = element[0].width; 
            }
            
            function draw(lX, lY, cX, cY){
                // line from
                ctx.moveTo(lX,lY);
                // to
                ctx.lineTo(cX,cY);
                // color
                ctx.strokeStyle = "#000000";
                // draw it
                ctx.stroke();
            }
            
            
            attrs.$observe("value", function (newValue) {
                ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            });

            attrs.$observe("saveVal", function (newValue, dnid) {
                var imagedata = ctx.canvas.toDataURL();
                $rootScope.signatureTemp.push({'dnid':dnid, 'signature':imagedata});
            });
            
            
        }
    };
}]);





/*
This is the file upload service. This service is called by the uploadFile() function
from the controller.js which in turn is called when the user clicks the upload button.
It is this service that will actually perform the file upload to the server.
*/
myApp.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function(file, uploadUrl, scope){
        
        var fd = new FormData();
        fd.append('file', file);
        
        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(data){
            
            if(data.status == "400") {
                alert(data.results);
            } else {
                scope.imagefilename = "uploads/"+data.results;
            }
            
         })
        .error(function(){ });
    }
}]);







myApp.factory('UserService', function($cookies,$state) {
    return {
        /*************************************************************
        *  These two variables may be needed to store user information
        *************************************************************/
        uname: '',
        fname: '',
        lname: '',
        uidentification: '',
        iduserroles: '',
        roledescription: '',
        assignedtoorg: '',
        
        /*************************************************************
        *  Log's the user out by clearing the 
        *  cookie. This service can be called in the controllers
        *************************************************************/
        logMeOut: function() {
            $cookies.remove("uname");
            $cookies.remove("fname");
            $cookies.remove("lname");
            $cookies.remove("uidentification");
            $cookies.remove("iduserroles");
            $cookies.remove("roledescription");
            $cookies.remove("assignedtoorg");
            
            $state.go('login');
        }
    };
});


myApp.factory('UtilityFunctions', function($cookies,$state) {
    return {
        getTheMonth: function($paramMonth) {
            if($paramMonth == 'January') {
                return "01";
            } else if($paramMonth == 'February') {
                return "02";
            } else if($paramMonth == 'March') {
                return "03";
            } else if($paramMonth == 'April') {
                return "04";
            } else if($paramMonth == 'May') {
                return "05";
            } else if($paramMonth == 'June') {
                return "06";
            } else if($paramMonth == 'July') {
                return "07";
            } else if($paramMonth == 'August') {
                return "08";
            } else if($paramMonth == 'September') {
                return "09";
            } else if($paramMonth == 'October') {
                return "10";
            } else if($paramMonth == 'November') {
                return "11";
            } else if($paramMonth == 'December') {
                return "12";
            }
        },
        generateUUID: function() {
            var d = new Date().getTime();
            var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = (d + Math.random()*16)%16 | 0;
                d = Math.floor(d/16);
                return (c=='x' ? r : (r&0x3|0x8)).toString(16);
            });
            return uuid;
        },
        getMonthName: function($paramMonth) {
            var months = new Array(12);
            months[0] = "January";
            months[1] = "February";
            months[2] = "March";
            months[3] = "April";
            months[4] = "May";
            months[5] = "June";
            months[6] = "July";
            months[7] = "August";
            months[8] = "September";
            months[9] = "October";
            months[10] = "November";
            months[11] = "December";
            
            return months[$paramMonth];
        },
        getDayName: function($paramDay) {
            var weekday = new Array(7);
            weekday[0]=  "Sunday";
            weekday[1] = "Monday";
            weekday[2] = "Tuesday";
            weekday[3] = "Wednesday";
            weekday[4] = "Thursday";
            weekday[5] = "Friday";
            weekday[6] = "Saturday";
            
            return weekday[$paramDay];
        },
        //var a = "10:15"
        //var b = toDate(a,"h:m")
        toDate: function(dStr,format) {
            var now = new Date();
            if (format == "h:m") {
                now.setHours(dStr.substr(0,dStr.indexOf(":")));
                now.setMinutes(dStr.substr(dStr.indexOf(":")+1));
                now.setSeconds(0);
                return now;
            }else 
                return "Invalid Format";
        }
        
    };
});


myApp.factory('EncryptionService', function($cookies,$state) {
    return {
        /*************************************************************
        *  This function is used to send encrypted strings to server 
        *  Works with API keys
        *************************************************************/
        hashhmac: function($requestString) {
            var hash = CryptoJS.HmacSHA256($requestString, "ASDFghjkl==");
            var hashInBase64 = CryptoJS.enc.Base64.stringify(hash);
            
            return hashInBase64;
        }
    };
});





