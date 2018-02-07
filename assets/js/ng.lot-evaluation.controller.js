(function (angular) {
    "use strict";

    var module = angular.module('lot-evaluation.controller', ['ngSanitize']);

    module.controller('LotEvaluationController',['$scope', '$http', 'RegistrationService', function($scope, $http, RegistrationService){
        $scope.data = {registrationStatusesNames: RegistrationService.registrationStatusesNames};
        $scope.defaultEvaluation = {status: 1};

        $scope.setRegistrationStatus = function(registration,status){

            if (confirm("Deseja realmente avaliar? As alterações não poderão ser desfeitas.") == true) {

                var checkedRegistrations = [];
                var evaluationStatus = $scope.getStatusSlug(status);
                var labels = MapasCulturais.gettext.moduleOpportunity;

                $(".evaluation:checked").each(function() {
                    checkedRegistrations.push($(this).val());
                });

                checkedRegistrations.forEach((registration)=>{
                    $http.post('/inscricoes/setStatusTo/' + registration, {status:evaluationStatus}).then(function (response) {
                    });
                });
                MapasCulturais.Messages.success(labels['changesSaved']);
                location.reload();

            } else {
                return false;
            }
        };

        $scope.getRegistrationStatus = function(registration){
            return registration.status;
        };

        $scope.getStatusSlug = function(status){
            switch (status.value){
                case 0: return 'draft'; break;
                case 1: return 'sent'; break;
                case 2: return 'invalid'; break;
                case 3: return 'notapproved'; break;
                case 8: return 'waitlist'; break;
                case 10: return 'approved'; break;
            }
        };

    }]);
    
})(angular);
