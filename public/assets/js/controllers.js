var loadAvgApp = angular.module('loadAvgApp', ['ui.router', 'ui.bootstrap', 'ui.bootstrap.pagination']);

// Setting up Object-based states
/*var users = {
  name: 'users',
  url: '/users',
  templateUrl: 'public/tpl/list-users.html',
  controller: 'ListController'
}*/

//
loadAvgApp.config(['$stateProvider', '$urlRouterProvider',
                  function($stateProvider, $urlRouterProvider){
  // For any unmatched url, redirect to /
  $urlRouterProvider
    .when('/u?id', '/users/:id')
    .otherwise("/");

  $stateProvider
    .state('home', {
      url: '/',
      templateUrl: 'public/tpl/home.html',
      // controller: 'ChartsController'
    })
    .state('users', {
      url: '/users',
      templateUrl: 'public/tpl/users.html',
      controller: 'ListController'
    })
    .state('users.new', {
      url: '/new',
      templateUrl: 'public/tpl/users.new.html',
      controller: 'AddController'
    })
    .state('users.server', {
      url: '/{user_id:[0-9]{1,4}}/servers/new',
      templateUrl: 'public/tpl/users.server.new.html',
      controller: 'AddController'
    })
    .state('users.detail', {
      url: '/{user_id:[0-9]{1,4}}',
      templateUrl: 'public/tpl/users.detail.html',
      controller: 'EditController'
    })
    .state('users.edit', {
      url: '/{user_id:[0-9]{1,4}}/edit',
      templateUrl: 'public/tpl/users.edit.html',
      controller: 'EditController'
    })
    .state('servers', {
      url: '/servers',
      templateUrl: 'public/tpl/servers.html',
      controller: 'ListController'
    })
    .state('servers.detail', {
      url: '/{server_id:[0-9]{1,4}}',
      templateUrl: 'public/tpl/servers.detail.html',
      controller: 'EditController'
    })
    .state('servers.edit', {
      url: '/{server_id:[0-9]{1,4}}/edit',
      templateUrl: 'public/tpl/servers.edit.html',
      controller: 'EditController'
    })
    .state('about', {
      url: '/about',
      templateUrl: 'public/tpl/about.html'
    });
}]);


//
loadAvgApp.run(['$rootScope', '$state', '$stateParams',
               function($rootScope, $state, $stateParams){
  // Adding references to $state and $stateParams to the
  // $rootScope so that you can access them from any scope
  // within the applications.
  $rootScope.$state = $state;
  $rootScope.$stateParams = $stateParams;
  $rootScope.app_name = "LoadAvg Server"
  $rootScope.app_version = "version 1.0"
}]);


// Filter
loadAvgApp.filter('startFrom', function() {
  return function(input, start) {
    if(input) {
      start = +start; //parse to int
      return input.slice(start);
    }
    return [];
  }
});


//
/*loadAvgApp.factory('Storage', function($q, $http) {
  var storage =  {};
  storage.get = function(callback) {
    if( ! storage.storedData){
      return $http.get('api/users').then(function(res){
        storage.storedData=res.data;
        return storage.storedData
      }).then(callback)
    } else {
      var def= $q.defer();
      def.done(callback);
      defer.resolve(storage.storedData);
      return def.promise;
    }
  }
  storage.set = function(obj) {
    // do ajax update and on success
    storage.storedData.push(obj);
  }
  return storage;
});*/


// Add Controller
loadAvgApp.controller('AddController',
                      function AddController($scope, $http, $location,
                                             $stateParams){
  var id = $stateParams.user_id;
  $scope.master = {};
  $scope.activePath = null;

  // Add a new user
  $scope.new_user = function(user, NewUserForm) {
    console.log(user);
    $http.post('api/users', user).success(function(){
      $scope.reset();
      $scope.activePath = $location.path('/users');
    });
    $scope.reset = function() {
      $scope.user = angular.copy($scope.master);
    };
    $scope.reset();
  };

  // Add a new server for a specified user
  $scope.new_server = function(server, NewServerForm) {
    console.log(server);
    $http.post('api/users/' + id + '/servers', server).success(function(){
      $scope.reset();
      $scope.activePath = $location.path('/users/' + id);
    });
    $scope.reset = function() {
      $scope.server = angular.copy($scope.master);
    };
    $scope.reset();
  };

});


// List controller
loadAvgApp.controller('ListController',
                      function ListController($scope, $http, $location,
                                              $timeout){

  // Return a list of all the users
  $http.get('api/users').success(function(data){
    // console.log(data);
    $scope.users = data;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 15;
    $scope.userFilteredItems = $scope.users.length;
    $scope.totalItems = $scope.users.length;
    $scope.maxSize = 5;
    $scope.bigTotalItems = $scope.users.length;
    $scope.bigCurrentPage = 1;
  });

  $http.get('api/servers').success(function(data){
    // console.log(data);
    $scope.servers = data;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 15;
    $scope.serverFilteredItems = $scope.servers.length;
    $scope.totalItems = $scope.servers.length;
    $scope.maxSize = 25;
    $scope.bigTotalItems = $scope.servers.length;
    $scope.bigCurrentPage = 1;
  });

  // callback for ng-click cancel
  $scope.cancel = function() {
    $location.path('/');
  };

  $scope.setPage = function(pageNo) {
    $scope.currentPage = pageNo;
  };

  $scope.pageChanged = function() {
    console.log('Page changed to: ' + $scope.currentPage);
  };

  $scope.filter = function() {
    $timeout(function(){
      $scope.userFilteredItems = $scope.filtered.length;
      $scope.serverFilteredItems = $scope.filtered.length;
    }, 10);
  };

  $scope.sort_by = function(predicate) {
    $scope.predicate = predicate;
    $scope.reverse = !$scope.reverse;
  };

});


// Edit controller
loadAvgApp.controller('EditController',
                      function EditController($scope, $http, $location, $stateParams){
  var uid = $stateParams.user_id;
  var sid = $stateParams.server_id;
  $scope.master = {};
  $scope.activePath = null;

  // Return a specified user
  $http.get('api/users/' + uid).success(function(data){
    $scope.user = data;
  });

  // Return a specified server
  $http.get('api/servers/' + sid).success(function(data){
    $scope.server = data;
  });

  // Return data for a specified server
  $http.get('api/servers/' + sid + '/data').success(function(data){
    console.log("server id: " + sid);
    $scope.server_data = data;
    $scope.currentPage = 1;
    $scope.itemsPerPage = 5;
    $scope.serverDataFilteredItems = $scope.server_data.length;
    $scope.totalItems = $scope.server_data.length;
    // $scope.maxSize = 25;
    // $scope.bigTotalItems = $scope.server_data.length;
    // $scope.bigCurrentPage = 1;
  });

  // Update specified user record
  $scope.update_user = function(user, EditUserForm) {
    console.log(user);
    $http.put('api/users/' + uid, user).success(function(data){
      $scope.user = data;
      $scope.activePath = $location.path('/users/' + uid);
    });
  };

  // Update specified server record
  $scope.update_server = function(server, EditServerForm) {
    console.log(server);
    $http.put('api/servers/' + sid, server).success(function(data){
      $scope.server = data;
      $scope.activePath = $location.path('/servers/' + sid);
    });
  };

  // Delete specififed user
  $scope.delete_user = function(user) {
    console.log(user);
    var deleteUser = confirm('Are you absolutely sure you want to remove this user?');

    if (deleteUser) {
      $http.delete('api/users/' + user.id);
      $scope.activePath = $location.path('/users');
    }
  };

  // Delete specififed server
  $scope.delete_server = function(server) {
    console.log(server);
    var deleteServer = confirm('Are you absolutely sure you want to remove this server?');

    if (deleteServer) {
      $http.delete('api/servers/' + server.id);
      $scope.activePath = $location.path('/servers');
    }
  };

});



















