<?php
    namespace App\Repository;

    use Illuminate\Database\Eloquent\Model;

     abstract class AbstractRepository
    {

        private $model;

           public function __construct(Model $model)
           {
               
               $this->model = $model;

           }

           public function selectConditions($conditions)
           {
                $expressions = explode(';', $conditions);

                $where = '';
                foreach($expressions as $e){
                    $exp = explode(':', $e);
                    if($exp[1] == 'ilike'){
                        $exp[2] = '%'. $exp[2] . '%';
                    }
                    $this->model = $this->model->where($exp[0], $exp[1], $exp[2]);
                }
           }

           public function joinConditions($orWhere)
           {
                $expressions = explode(';', $orWhere);

                $where = '';
                foreach($expressions as $e){
                    $exp = explode(':', $e);
                    $this->model = $this->model->orWhere($exp[0], $exp[1], $exp[2]);
                }
           }


           public function selectFilter($filters)
           {
                $this->model = $this->model->selectRaw($filters);

           }

           public function getResult()
           {
               return $this->model;
           }
    }
