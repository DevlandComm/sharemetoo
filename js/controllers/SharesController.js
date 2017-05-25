'use strict';


// projects.html
clientControllers.controller('SharesController', ['$scope', '$http', '$state', 'fileUpload', 'loginService', 'UtilityFunctions', 'EncryptionService', '$cookies', '$log', '$timeout', '$sce', 'myconfig', 'SweetAlert', 'UserSessionService', function($scope, $http, $state, fileUpload, loginService, UtilityFunctions, EncryptionService, $cookies, $log, $timeout, $sce, myconfig, SweetAlert, UserSessionService) {
  
    if( loginService.islogged() == false ) {
        $state.go('login');
    }
    
    
    // Set variables.
    $scope.displayName = UserSessionService.getFname();
    $scope.mypic = UserSessionService.getPicture();
    $scope.allFriends = [];
    $scope.allsubscription = [];
    $scope.sharedUsers = [];
    $scope.sharedUsersAndOthers = [];
    $scope.OtherUsersToBeInvited = [];

    $scope.userSharedWith = {
        selected:{}
    };

    $scope.matrix = "";
    $scope.tTitle    = "";
    $scope.tUserName = "";
    $scope.tPassword = "";
    $scope.tURL = "";
    $scope.tDescription = "";
    
    $scope.teIdno     = "";
    $scope.teTitle    = "";
    $scope.teUserName = "";
    $scope.tePassword = "";
    $scope.teURL = "";
    $scope.teDescription = "";
    
    //Setup post headers
    var config = { 
        headers : {'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'}
    };

    $scope.rowlimit = 100;
    
    
    $scope.searchquery = "";
    
    //////////////////////////////////////////////////////////////
    // BEGIN: Initialize Modal View
    var modalinvite  = new RModal(document.getElementById('modalinvite'), {
        beforeOpen: function(next) {
            console.log('beforeOpen');
            next();
        }
        , afterOpen: function() {
            console.log('opened');
        }

        , beforeClose: function(next) {
            console.log('beforeClose');
            
            next();
        }
        , afterClose: function() {
            console.log('closed');
        }
    });
    window.modalinvite = modalinvite;



    var modaladdfriendstosubscription = new RModal(document.getElementById('modaladdfriendstosubscription'), {
        beforeOpen: function(next) {
            console.log('beforeOpen');
            next();
        }
        , afterOpen: function() {
            console.log('opened');
        }

        , beforeClose: function(next) {
            console.log('beforeClose');
            
            next();
        }
        , afterClose: function() {
            console.log('closed');
        }
    });
    window.modaladdfriendstosubscription = modaladdfriendstosubscription;




    var modal = new RModal(document.getElementById('modal'), {
        beforeOpen: function(next) {
            console.log('beforeOpen');
            next();
        }
        , afterOpen: function() {
            console.log('opened');
        }

        , beforeClose: function(next) {
            console.log('beforeClose');
            
            /*document.getElementById("btnDeleteAgency").classList.remove("hide");
            document.getElementById("btnSaveAgency").classList.add("hide");
            document.getElementById("btnEditAgency").classList.remove("hide");
            document.getElementById("btnCancelAgency").classList.add("hide");*/
            
            next();
        }
        , afterClose: function() {
            console.log('closed');
        }
    });
    window.modal = modal;
    
    
    var modaledit = new RModal(document.getElementById('modaledit'), {
        beforeOpen: function(next) {
            console.log('beforeOpen');
            next();
        }
        , afterOpen: function() {
            console.log('opened');
        }

        , beforeClose: function(next) {
            console.log('beforeClose');
            
            /*document.getElementById("btnDeleteAgency").classList.remove("hide");
            document.getElementById("btnSaveAgency").classList.add("hide");
            document.getElementById("btnEditAgency").classList.remove("hide");
            document.getElementById("btnCancelAgency").classList.add("hide");*/
            
            next();
        }
        , afterClose: function() {
            console.log('closed');
        }
    });
    window.modaledit = modaledit;
    
    // END: Initialize Modal View
    //////////////////////////////////////////////////////////////
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Get All Friends
    $scope.GetAllFriends = function() {
        
        var allFriends = $.param({
            action:'all_friends',
            userid:UserSessionService.getUidentification()
        });
        $http.post('server/application.php',allFriends,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                $scope.allFriends = data.results;
            } else {
                var err = "Error : " + data.results;
                swal(err);
            }

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });
    };
    // END: Get All Friends
    ////////////////////////////////////////////////////////////////
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Get All Subcriptions
    $scope.GetSubscription = function() {
        
        var allSubscription = $.param({
            action:'my_shared_items',
            userid:UserSessionService.getUidentification()
        });
        $http.post('server/application.php',allSubscription,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                $scope.allsubscription = data.results;
                
                //Let's load the results into the martix array by two's
                $scope.matrix = $scope.listToMatrix($scope.allsubscription, 2);
                //angular.forEach($scope.allsubscription, function(item) {    
                //});
                
            } else {
                var err = "Error : " + data.results;
                swal(err);
            }

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });
    };
    // END: Get All Subcriptions
    ////////////////////////////////////////////////////////////////
    
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Save Subscription
    $scope.saveSubscription = function() {
        
        var subscription = $.param({
            action:'new_shared_items',
            title:$scope.tTitle,
            userid:UserSessionService.getUidentification(),
            uname:$scope.tUserName,
            pw:$scope.tPassword,
            sharedurl:$scope.tURL,
            description:$scope.tDescription
        });
        $http.post('server/application.php',subscription,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                swal('New subscription added!');
                $scope.GetSubscription();
                $scope.refresh;
                modal.close();
                
            } else {
                var err = "Error : " + data.results;
                swal(err);
            }

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });
    };
    // END: Save Subscription
    ////////////////////////////////////////////////////////////////
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Save Edited Subscription
    $scope.saveEditedSubscription = function() {
        
        var editedsubscription = $.param({
            action:'update_shared_items',
            idno:$scope.teIdno,
            title:$scope.teTitle,
            userid:UserSessionService.getUidentification(),
            uname:$scope.teUserName,
            pw:$scope.tePassword,
            sharedurl:$scope.teURL,
            description:$scope.teDescription
        });
        $http.post('server/application.php',editedsubscription,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                swal('Subscription updated!');
                $scope.GetSubscription();
                $scope.refresh;
                modaledit.close();
                
            } else {
                var err = "Error : " + data.results;
                swal(err);
            }

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });
    };
    // END: Save Edited Subscription
    ////////////////////////////////////////////////////////////////
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Delete Subscription
    $scope.deleteThis = function(item) {
        
        swal({   
            title: "Delete this entry?",   
            text: "Are you sure you want to delete this entry?",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                
                var delSubscription = $.param({
                    action:'delete_shared_items',
                    userid:UserSessionService.getUidentification(),
                    idno:item.idno
                });
                $http.post('server/application.php',delSubscription,config).success(function(data,status,headers,config) {

                    if(data.status == "200")
                    {
                        swal('Subscription deleted!');
                        
                        //var indexx = $scope.matrix.indexOf(item);
                        //$scope.matrix.splice(indexx, 1);  
                        //$state.reload();
                        $scope.GetSubscription();
                        $scope.refresh;   
                    } else {
                        var err = "Error : " + data.results;
                        swal(err);
                    }

                }).error(function(data,status,headers,config) {
                    swal('Error : ' + status);
                });
                
            } 
        });
        
        
    };
    // END: Delete Subscription
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Get Shared Item Users
    $scope.GetSharedItemUsers = function(psharedid) {
        $scope.sharedUsers = [];
        
        var sharedItemUsers = $.param({
            action:'my_shared_items_users',
            userid:UserSessionService.getUidentification()
        });
        
        $http.post('server/application.php',sharedItemUsers,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                $scope.sharedUsers = data.results;
                
            } else {
                $scope.sharedUsers = [];
            }

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });
        
        //return sharedUsers;
    };
    // END: Get Shared Item Users
    ////////////////////////////////////////////////////////////////
    
    
    
    
    
    
    
    
    
    $scope.loadMore = function() {
        $scope.rowlimit += 100;
        
        if($scope.rowlimit > $scope.projects.length)
            document.getElementById("btnLoadMore").style.display = "none";
        else 
            document.getElementById("btnLoadMore").style.display = "block";
        
        if($scope.showNoPermitOnly) {
            $scope.getAllProjectsNoPermit();
        } else {
            $scope.getAllProjects();
        }
    };
    
    
    $scope.showMore = function() {
        $scope.rowlimit += 100;
        
        if($scope.rowlimit > $scope.projects.length)
            document.getElementById("btnLoadMore").style.display = "none";
        else 
            document.getElementById("btnLoadMore").style.display = "block";
        
        if($scope.showNoPermitOnly) {
            $scope.getAllProjectsNoPermit();
        } else {
            $scope.getAllProjects();
        }
    };
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Shows the Add Subscription Modal box
    $scope.showAddSubscription = function() {
        
        //Let's clear the input text boxes first
        $scope.tTitle       = "";
        $scope.tUserName    = "";
        $scope.tPassword    = "";
        $scope.tURL         = "";
        $scope.tDescription = "";
        
        modal.open();
    };
    // END: Shows the Add Subscription Modal box
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Shows the Edit Subscription Modal box
    $scope.showEditSubscription = function(item) {
        
        //Let's clear the input text boxes first
        $scope.teIdno        = item.idno;
        $scope.teTitle       = item.title
        $scope.teTitle       = item.title
        $scope.teUserName    = item.uname;
        $scope.tePassword    = item.pw;
        $scope.teURL         = item.sharedurl;
        $scope.teDescription = item.description;
        
        modaledit.open();
    };
    // END: Shows the Edit Subscription Modal box
    ////////////////////////////////////////////////////////////////
    
    
    
    
    $scope.doSearch = function() {
        $scope.rowlimit = 0;
        
        if($scope.showNoPermitOnly) {
            $scope.getAllProjectsNoPermit();
        } else {
            $scope.getAllProjects();
        }
        
        $scope.rowlimit = 100;
        
        //Scroll to top of the table
        var tableDiv = document.getElementById("tableDiv");
        tableDiv.scrollTop = 0;
    };
    
    
    
    $scope.listToMatrix = function(list, elementsPerSubArray) {
        var matrix = [], i, k;

        for (i = 0, k = -1; i < list.length; i++) {
            if (i % elementsPerSubArray === 0) {
                k++;
                matrix[k] = [];
            }

            matrix[k].push(list[i]);
        }

        console.log(matrix);
        
        /*var i = 0;
        angular.forEach(matrix, function(item) {
            var it = item[0];
            console.log("Object value: " + it.description);
        });*/
        
        return matrix;
    };
    

    $scope.addFriendsToSubscription = function(idno) {

        $scope.currentlySelectedShare = idno;
        $scope.sharedUsersAndOthers = [];
        
        for(var i=0; i<$scope.allFriends.length;i++) {
            var item = $scope.allFriends[i];
            var newItem = item;
            newItem['checked'] = false;
            angular.forEach($scope.sharedUsers, (ix) => {
                if ((item.userid == ix.id_shared_with) && (ix.id_shared_item == idno)) 
                    newItem['checked'] = true;
            });
            $scope.userSharedWith.selected[newItem.userid] = newItem.checked; //Initialize checkbox ng-model
            $scope.sharedUsersAndOthers.push(newItem);            
        }

        console.log(JSON.stringify($scope.sharedUsersAndOthers));

        modaladdfriendstosubscription.open();
    };


    $scope.shareUnshareItem = function(uuid) {
        console.log("shareUnshareItem clicked! UserID: "+uuid);
        console.log(JSON.stringify($scope.userSharedWith.selected));

        var isChecked = $scope.userSharedWith.selected[uuid];
        console.log("isChecked: "+isChecked);

        if (isChecked) {
            var sharedItemUsers = $.param({
                action:'share_item_to_friend',
                userid:uuid,
                shareditemid: $scope.currentlySelectedShare
            });
            
            $http.post('server/application.php',sharedItemUsers,config).success(function(data,status,headers,config) {

                if(data.status == "200")
                {
                      $scope.GetSharedItemUsers();
                } 

            }).error(function(data,status,headers,config) {
                swal('Error : ' + status);
            });
        } else {
            var sharedItemUsers = $.param({
                action:'delete_friend_share',
                userid:uuid,
                shareditemid: $scope.currentlySelectedShare
            });
            
            $http.post('server/application.php',sharedItemUsers,config).success(function(data,status,headers,config) {

                if(data.status == "200")
                {
                      $scope.GetSharedItemUsers();
                } 

            }).error(function(data,status,headers,config) {
                swal('Error : ' + status);
            });
        }
        

    };




    $scope.inviteUser = function() {

        var invite = $.param({
            email:$scope.txtInviteByEmail,
            userid:UserSessionService.getUidentification()
        });
        
        $http.post('server/inviteuser.php',invite,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                  console.log("Results: "+data.results);
            } 

            modaladdfriendstosubscription.close();

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });
    };



    $scope.searchUsersToBeInvited = function() {

        console.log("I'm clicked");

        var invite = $.param({
            action:"all_users_exlude_friends",
            searchterms:$scope.search_friend_to_invite,
            userid:UserSessionService.getUidentification()
        });
        
        $http.post('server/users.php',invite,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                  $scope.OtherUsersToBeInvited = data.results;
            } 

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });

    }


    $scope.showInviteUser = function() {

        modalinvite.open();
    }




    $scope.doInviteFriends = function() {

        /*var invite = $.param({
            email:$scope.inviteEmail,
            fname:$scope.inviteFname,
            lname:$scope.inviteLname,
            userid:UserSessionService.getUidentification()
        });
        
        $http.post('server/inviteuser2.php',invite,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                  console.log("Results: "+data.results);
                  swal('Invitation sent... '+data.results);
            } 

            modalinvite.close();

        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });*/

        
    }





    
    $scope.hoverIn = function(){
        this.hoverEdit = true;
    };

    $scope.hoverOut = function(){
        this.hoverEdit = false;
    };
    
    //Let's get the users we shared with.
    $scope.GetSharedItemUsers();
    
    //Get all friends
    $scope.GetAllFriends();
    
    //Get all subscription
    $scope.GetSubscription();
    
    

}]);

