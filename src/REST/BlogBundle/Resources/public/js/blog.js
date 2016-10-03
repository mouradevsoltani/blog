/**
 * Déclaration du module blogModule
 */
var app = angular.module("blogModule", ["ngSanitize"]).config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
});

/**
 * Déclaration du controller blogController
 */
app.controller("blogController", function ($scope, $http) {
    $http({
        method: "GET",
        url: Routing.generate('api_blog_articles') //récupération des articles en format json
    }).then(function mySucces(response) {
        console.log(response.data);
        $scope.articles = response.data;
    }, function myError(response) {
        console.log(response.data);
        $scope.articles = response.statusText;
    });
});
