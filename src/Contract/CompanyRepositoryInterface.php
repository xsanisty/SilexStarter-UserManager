<?php

namespace Xsanisty\UserManager\Contract;

use Exception;

use Xsanisty\UserManager\Model\Company;

interface CompanyRepositoryInterface
{

    /**
     * Find records by id.
     *
     * @param  integer  $id     The record id
     *
     * @return Company          Model of the specified record
     */
    public function findById($id);

    /**
     * Create new record based on given data.
     *
     * @param  array    $data   The data need to be created
     *
     * @return Company          The generated model
     * @throws Exception        Exception thrown if operation failed
     */
    public function create(array $data);

    /**
     * Update specific record related to given model
     *
     * @param  integer  $id     The id of the model
     * @param  array    $data   The model object need to be updated.
     *
     * @return Company          The updated model
     * @throws Exception        Exception thrown if operation failed
     */
    public function update($id, array $data);

    /**
     * Delete records by id.
     *
     * @param  integer  $id     The record id
     *
     * @return Company          Model of the specified record
     * @throws Exception        Exception thrown if operation failed
     */
    public function delete($id);

    /**
     * Create a query to be consumed by datatable library.
     */
    public function createDatatableQuery();
}