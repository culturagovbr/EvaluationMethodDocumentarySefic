<div class="registration-fieldset" ng-controller="CategoriesController">
    <h4>Áreas de atuação e segmentos<span id="segment-required">*</span></h4>
    <div ng-repeat="tipologia in tipologiaAtuacao">
        <a class="required editable js-editable" id="category" data-segmento="{{tipologia.segmento}}" ng-click="editBox.open('eb-tipologia'+$index, $event)">{{tipologia.segmento ? tipologia.nomeSegmento : 'Escolha um segmento'}}</a>

        <edit-box  id="eb-tipologia{{$index}}" position="bottom" cancel-label="Cancelar" submit-label="Enviar" on-submit="setTypes" on-cancel="resetValues" close-on-cancel="1">
            <label>
                área:
                <select ng-model="tipologia.area" ng-change="set($index)">
                    <option ng-repeat="(key, val) in tipologia._areas" ng-value="key">{{key}}</option>
                </select>
            </label>
            <label ng-show="tipologia.area">
                segmento:
                <select ng-change="findName(tipologia)" ng-model="tipologia.segmento">
                    <option ng-repeat="(key, val) in tipologia._segmentos" ng-value="key">{{val}}</option>
                </select>
            </label>

        </edit-box>

    </div>
    <a class="btn btn-default add" ng-click="adicionarSegmento()">Novo Segmento</a>
    <a ng-if="tipologiaAtuacao.length > 1" class="btn btn-danger delete" ng-show="tipologiaAtuacao" ng-click="removerSegmento()">Remover</a>
</div>