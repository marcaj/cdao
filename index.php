<?php
require_once 'cdao/CDAO.php';
require_once 'cdao/model_cbase.php';

use cdao\CDAO;
use cdao\model_cbase;

class Test extends CDAO {}
class model_test extends model_cbase {}

$t0 = new Test();
$t0->setProperty('test property');
$t0->save();

$t1 = Test::getById(0);
echo $t1->getProperty();
