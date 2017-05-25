
	
var myApp = angular.module('myApp', ['ngCookies','ui.router','clientControllers','720kb.datepicker','ngWYSIWYG','oitozero.ngSweetAlert','mdr.file']);

myApp.constant('myconfig',{
    appName:'ShareMeToo',
    appVersion: '1.0',
    asset: 'ShareMeToo'
});


myApp.value("permittingArrays", {
    projectNumbers: []
});

myApp.config(['$stateProvider', '$urlRouterProvider', '$httpProvider', function($stateProvider, $urlRouterProvider, $httpProvider) {
	/*BEGIN: For Cookes*/
    $httpProvider.defaults.xsrfCookieName = 'csrftoken';
    $httpProvider.defaults.xsrfHeaderName = 'X-CSRFToken';
    /*END: For Cookes*/
    
	$urlRouterProvider.otherwise('/shares');
 
    $stateProvider
        .state('login', {
            url:'/login',
            templateUrl: 'partials/login.html',
            controller: 'LoginController'
        })
        .state('adminmanageuser', {
            url:'/adminmanageuser',
            views: {
                '': {templateUrl: 'partials/adminmanageuser.html', controller: 'AdminManageUserController'},
                'topnav@adminmanageuser': {templateUrl:'partials/assets/topnav.html', controller:'TopNavController'},
                'nav@adminmanageuser': {templateUrl: 'partials/assets/nav.html', controller: 'NavController'},
                'footer@adminmanageuser': {templateUrl: 'partials/assets/footer.html'}
            }
        })
        .state('registeruser', {
            url:'/registeruser/:email',
            templateUrl: 'partials/registernew.html',
            controller: 'RegisterUserController'
        })
        .state('forgotpassw', {
            url:'/forgotpassw/:email',
            templateUrl: 'partials/forgotpassw.html',
            controller: 'ForgotPasswUserController'
        })
        .state('requestpasswreset', {
            url:'/requestpasswreset',
            templateUrl: 'partials/passwresetrequest.html',
            controller: 'ForgotPasswUserController'
        })
        .state('shares', {
            url:'/shares',
            views: {
                '': {templateUrl: 'partials/shares.html', controller: 'SharesController'},
                'topnav@shares': {templateUrl:'partials/assets/topnav.html', controller:'TopNavController'},
                'nav@shares': {templateUrl: 'partials/assets/nav.html', controller: 'NavController'},
                'footer@shares': {templateUrl: 'partials/assets/footer.html'}
            }
        })
        .state('myinfo', {
            url:'/myinfo',
            views: {
                '': {templateUrl: 'partials/myinfo.html', controller: 'MyInfoController'},
                'topnav@myinfo': {templateUrl:'partials/assets/topnav.html', controller:'TopNavController'},
                'nav@myinfo': {templateUrl: 'partials/assets/nav.html', controller: 'NavController'},
                'footer@myinfo': {templateUrl: 'partials/assets/footer.html'}
            }
        });
  
}]);





/*
myApp.run(function($rootScope, $location, loginService, $state) {
    
    $rootScope.$on('$routeChangeStart', function() {
        if( loginService.islogged() == false ) {
            $state.go('login');
        }
    });
    
});*/
