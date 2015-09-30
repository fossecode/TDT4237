<?php
namespace tdt4237\webapp\repository;

interface RepositoryInterface
{
    public function find($id);
    public function save($record);
    public function remove($id);
}
