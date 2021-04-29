<?php


namespace App\Repositories\Interfaces;


interface DocketRepositoryInterfaces
{
    public function index($companyId);

    public function getDocketTemplateByUserId($userId);

    public function show($id);



}
