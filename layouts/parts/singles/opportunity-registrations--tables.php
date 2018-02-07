<?php $slug = $entity->evaluationMethodConfiguration->getEvaluationMethod()->getSlug(); ?>
<div id="inscritos" class="aba-content">
    <?php if ($entity->canUser('@control')): ?>
        <?php if($slug === "documentary-sefic"){
          $this->part('singles/opportunity-registrations--tables--manager-sefic', ['entity' => $entity]);
        }else{
          $this->part('singles/opportunity-registrations--tables--manager', ['entity' => $entity]);
        }
        ?>
        <?php $this->part('singles/opportunity-registrations--publish-button', ['entity' => $entity]) ?>

    <?php elseif ($entity->publishedRegistrations): ?>
        <?php $this->part('singles/opportunity-registrations--tables--published', ['entity' => $entity]) ?>
    <?php endif; ?>
</div>
<!--#inscritos-->
