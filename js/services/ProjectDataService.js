'use strict';

myApp.service("ProjectDataService", function($http, $q, UserSessionService) {
    var deferred = $q.defer();
    
    var projnos = $.param({
        action:'get_projectnumbers',
        userid:UserSessionService.getUidentification()
    });
    $http.post('server/application.php',projnos,config).success(function(data,status,headers,config) {

        if(data.status == "200")
        {
            $scope.projectnos = data.results;
        } else {
            var err = "Error : " + data.results;
            swal(err);
        }
        document.getElementById("divProgress2").classList.remove("show");
    }).error(function(data,status,headers,config) {
        swal('Error : ' + status);
        document.getElementById("divProgress2").classList.remove("show");
    });
});