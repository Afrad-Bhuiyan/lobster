<?php 
  
    class user extends database{

        private $table_name="users";

        //select data from database
        public function select($param = null){

            $values=array(
                "table_name"=>$this->table_name
            );

            if($param !== null){
                //merge table name and pass parameters togther
                $values=array_merge($values, $param);
            }

            return $this->get_data($values);
        }

        //insert data from database
        public function insert($param){

            $table_name=array(
                "table_name"=>$this->table_name
            );

            //merge table name and pass parameters togther
            $values=array_merge($table_name, $param);

            return $this->insert_data($values);

        }

        //update data from database
        public function update($param){

            $table_name=array(
                "table_name"=>$this->table_name
            );

            //merge table name and pass parameters togther
            $values=array_merge($table_name, $param);

            return $this->update_data($values);

        }

        //delete data from database
        public function delete($param){

            $table_name=array(
                "table_name"=>$this->table_name
            );

            //merge table name and pass parameters togther
            $values=array_merge($table_name, $param);

            return $this->delete_data($values);
        }

    }
?>
