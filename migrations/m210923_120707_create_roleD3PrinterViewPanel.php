<?php



use yii\db\Migration;
use d3yii2\d3printer\accessRights\D3PrinterViewPanelUserRole;

class m210923_120707_create_roleD3PrinterViewPanel  extends Migration {

    public function up() {

        $auth = Yii::$app->authManager;
        $role = $auth->createRole(D3PrinterViewPanelUserRole::NAME);
        $auth->add($role);

    }

    public function down() {
        $auth = Yii::$app->authManager;
        $role = $auth->createRole(D3PrinterViewPanelUserRole::NAME);
        $auth->remove($role);
    }
}
