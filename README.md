CDAO
====

### Description 

This is a powerfull framework to easy create/read/update/delete data from database. 

### Manual

*   Create a class to store your data and logic

    class Test extends CDAO {}
    
*   Create a model class extending model_model
 
    class model_test extends model_cbase {}
    
*   Now you can easly use your class
 
    $t0 = new Test();
    $t0->setProperty('test property');
    $t0->save();

    $t1 = Test::getById(0);
    echo $t1->getProperty();
