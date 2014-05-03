var loadAvgApp = angular.module('loadAvgApp', ['ui.router']);

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
      controller: 'ServersController'
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

// List controller
loadAvgApp.controller('ListController',
                      function ListController($scope, $http, $location){
  // Return a list of all the users
  $http.get('api/users').success(function(data){
    $scope.users = data;
  });

  // callback for ng-click cancel
  $scope.cancel = function() {
    $location.path('/');
  };

});


// Edit controller
loadAvgApp.controller('EditController',
                      function EditController($scope, $http, $location, $stateParams){
  var id = $stateParams.user_id;
  $scope.master = {};
  $scope.activePath = null;

  // Return a specified user
  $http.get('api/users/' + id).success(function(data){
    $scope.user = data;
  });

  // Update specified user record
  $scope.update_user = function(user, EditUserForm) {
    console.log(user);
    $http.put('api/users/' + id, user).success(function(data){
      $scope.user = data;
      $scope.activePath = $location.path('/users/' + id);
    });
  };

  // Delete specififed user
  $scope.delete_user = function(user) {
    console.log(user);
    var deleteUser = confirm('Are you absolutely sure you want to destroy this user?');

    if (deleteUser) {
      $http.delete('api/users/' + user.id);
      $scope.activePath = $location.path('/users');
    }
  };

});


// Add Controller
loadAvgApp.controller('AddController',
                      function AddController($scope, $http, $location){
  $scope.master = {};
  $scope.activePath = null;

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
});



















