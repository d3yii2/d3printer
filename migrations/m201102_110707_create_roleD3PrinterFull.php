<?php


use d3yii2\d3printer\accessRights\D3PrinterFullUserRole;
use yii\db\Migration;

class m201102_110707_create_roleD3PrinterFull extends Migration
{
    
    public function up()
    {
        
        $auth = Yii::$app->authManager;
        $role = $auth->createRole(D3PrinterFullUserRole::NAME);
        $auth->add($role);
        
    }
    
    public function down()
    {
        $auth = Yii::$app->authManager;
        $role = $auth->createRole(D3PrinterFullUserRole::NAME);
        $auth->remove($role);
    }
}
