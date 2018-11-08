<?php
namespace EvaluationMethodDocumentarySefic;

use MapasCulturais\i;
use MapasCulturais\App;
use MapasCulturais\Entities;

const STATUS_INVALID = 'invalid';
const STATUS_VALID = 'valid';

class Plugin extends \EvaluationMethodDocumentary\Plugin {


    public function getSlug() {
        return 'documentary-sefic';
    }

    public function getName() {
        return i::__('Avaliação Documental Sefic');
    }

    public function getDescription() {
        return i::__('Consiste num checkbox e um textarea para cada campo do formulário de inscrição.');
    }

    public function cmpValues($value1, $value2){
        $value1 = (float) $value1;
        $value2 = (float) $value2;

        return parent::cmpValues($value1, $value2);
    }

    public function getConfigurationFormPartName() {
        return ;
    }

    protected function _register() {
        ;
    }

    function getValidationErrors(Entities\EvaluationMethodConfiguration $evaluation_method_configuration, array $data){
        $errors = [];
        $empty = true;
        $obs_empty = false;
        foreach($data as $prop => $val){
            if($val['evaluation']){
                $empty = false;
            }


            if(!$val['evaluation'] == "" && $val['obs'] == ""){
                $obs_empty = true;
            }
        }

        if($empty){
            $errors[] = i::__('Nenhum campo foi avaliado');
            $obs_empty = false;
        }

        if($obs_empty){
            $errors[] = 'É necessário preencher a justificativa dos campos avaliados';
        }

        return $errors;
    }

    function enqueueScriptsAndStyles() {
        $app = App::i();

        $app->view->enqueueScript('app', 'documentary-evaluation-form', 'js/evaluation-form--documentary.js', ['entity.module.opportunity']);
        $app->view->enqueueStyle('app', 'documentary-evaluation-method', 'css/documentary-sefic-evaluation-method.css');


        $app->view->enqueueScript('app', 'opportunity-controller-categories', 'js/ng.opportunity.controller.categories.js', ['entity.module.opportunity']);
        $app->view->enqueueScript('app', 'registration-controller', 'js/ng.registration.controller.js', ['entity.module.opportunity']);
        $app->view->enqueueScript('app', 'lot-evaluation-controller', 'js/ng.lot-evaluation.controller.js', ['entity.module.opportunity']);

        $app->view->jsObject['angularAppDependencies'][] = 'opportunity.controller.categories';
        $app->view->jsObject['angularAppDependencies'][] = 'registration.controller';
        $app->view->jsObject['angularAppDependencies'][] = 'lot-evaluation.controller';
        $app->view->jsObject['segmentos'] = require __DIR__ . '/' . 'tipologia-oportunidades.php';
    }

    public function _init() {
        $app = App::i();
        $app->hook('evaluationsReport(documentary).sections', function(Entities\Opportunity $opportunity, &$sections) use($app) {
            $columns = [];
            $evaluations = $opportunity->getEvaluations();

            foreach($evaluations as $eva){
                $evaluation = $eva['evaluation'];
                $data = (array) $evaluation->evaluationData;
                foreach($data as $id => $d){
                    $columns[$id] = $d['label'];
                }
            }

            $result = [
                'registration' => $sections['registration'],
                'committee' => $sections['committee'],
            ];


            foreach($columns as $id => $col){
                $result[$id] = (object) [
                    'label' => $col,
                    'color' => '#EEEEEE',
                    'columns' => [
                        'val' => (object) [
                            'label' => i::__('Avaliação'),
                            'getValue' => function(Entities\RegistrationEvaluation $evaluation) use($id) {
                                $evaluation_data = (array) $evaluation->evaluationData;

                                if(isset($evaluation_data[$id])){
                                     $data = $evaluation_data[$id];

                                     if($data['evaluation'] == 'valid'){
                                         return i::__('Válida');
                                     } else if($data['evaluation'] == 'invalid') {
                                         return i::__('Inválida');
                                     } else {
                                         return '';
                                     }
                                } else {
                                    return '';
                                }
                            }
                        ],
                        'obs' => (object) [
                            'label' => i::__('Observações'),
                            'getValue' => function(Entities\RegistrationEvaluation $evaluation) use($id) {

                                $evaluation_data = (array) $evaluation->evaluationData;
                                if (isset($evaluation_data[$id])) {
                                    $data = $evaluation_data[$id];
                                    return $data['obs'];
                                } else {
                                    return '';
                                }
                            }
                        ],
                    ]
                ];
            }

            $result['evaluation'] = $sections['evaluation'];

            $sections = $result;
        });

        $app->hook('view.partial(singles/registration-single--header).params', function (&$params, &$template_name) {
            $opportunity = self::getRequestedOpportunity();

            if (!$opportunity) {
                return;
            }

            if ($opportunity->slug == 'documentary-sefic') {
                $template_name = 'singles/registration-single--header-sefic';
            }
        });

        $app->hook('view.partial(singles/registration-single--categories).params', function (&$params, &$template_name) {
            $opportunity = self::getRequestedOpportunity();

            if (!$opportunity) {
                return;
            }

            if ($opportunity->slug == 'documentary-sefic') {
                $template_name = 'singles/registration-single--categories-sefic';
            }
        });

        $app->hook('view.partial(singles/registration--sidebar--right).params', function (&$params, &$template_name) {
            $opportunity = self::getRequestedOpportunity();

            if (!$opportunity) {
                return;
            }

            if ($opportunity->slug == 'documentary-sefic') {
                $template_name = 'singles/registration--sidebar--right-sefic';
            }
        });
    }

    public function _getConsolidatedResult(Entities\Registration $registration) {
        $app = App::i();

        $evaluations = $app->repo('RegistrationEvaluation')->findBy(['registration' => $registration]);

        if(is_array($evaluations) && count($evaluations) === 0){
            return 0;
        }

        $result = 1;

        foreach ($evaluations as $eval){
            if($eval->status === \MapasCulturais\Entities\RegistrationEvaluation::STATUS_DRAFT){
                return 0;
            }

            $result = ($result === 1 && $this->getEvaluationResult($eval) === 1) ? 1 : -1;
        }

        return $result;
    }

    public function getEvaluationResult(Entities\RegistrationEvaluation $evaluation) {
        $data = (array) $evaluation->evaluationData;

        if(is_array($data) && count($data) == 0){
            return 1; // valid
        }

        foreach ($data as $id => $value) {
            if(isset($value['evaluation']) && $value['evaluation'] === STATUS_INVALID){
                return -1;
            }
        }

        return 1;
    }

    public function valueToString($value) {

        if($value == 1){
            return i::__('Válida');
        } else if($value == -1){
            return i::__('Inválida');
        }

        return '';

    }

    public function fetchRegistrations() {
        return true;
    }

    static function getRequestedOpportunity()
    {
        $app = App::i();

        $opportunity = $app->view->controller->requestedEntity->opportunity;
        $opportunity->slug = $opportunity->evaluationMethodConfiguration->getEvaluationMethod()->getSlug();

        if (!$opportunity) {
            return null;
        }

        return $opportunity;
    }
}
