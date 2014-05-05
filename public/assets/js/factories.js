angular.module('loadAvgApp')

  //
  .factory('utils', function () {
    return {

      // Util for finding an object by its 'id' property among an array
      findById: function findById(a, id) {
        for (var i = 0; i < a.length; i++) {
          if (a[i].id == id) return a[i];
        }
        return null;
      }

    };
  })

  //
  .factory('Storage', function($q,$http) {
    var storage =  {};
    storage.get = function(callback) {

      if(!storage.storedData){
        return $http.get('api/users').then(function(res){
          storage.storedData=res.data;
          return storage.storedData
        }).then(callback)
      }else{
        var def= $q.defer();
        def.done(callback);
        defer.resolve(storage.storedData);
        return def.promise;
      }


    }
    storage.set = function(obj) {
     /* do ajax update and on success*/
        storage.storedData.push(obj);
    }
    return storage;
  })

