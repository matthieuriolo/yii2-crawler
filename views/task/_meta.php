<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;




echo $this->render('/meta/_form', [
    'model' => $model,
]);

?>

<hr>

<?php

echo $this->render('/meta/_list', [
    'dataProvider' => $dataProvider,
]);

?>
