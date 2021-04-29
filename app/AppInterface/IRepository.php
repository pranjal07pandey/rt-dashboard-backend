<?php
namespace App\AppInterface;

    interface IRepository {
        public function getModel();
        public function getDataById($request = null);
        public function insertAndUpdate($request = null);
        public function getDataWhere($array = []);
        public function deleteDataById($request = null);
    }
?>

