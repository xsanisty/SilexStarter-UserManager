<?php

namespace Xsanisty\UserManager\Migration;

use SilexStarter\Migration\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run upgrade migration.
     */
    public function up()
    {
        $this->schema->create(
            'companies',
            function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->string('logo');
                $table->text('address');
                $table->text('address_additional');
                $table->string('city');
                $table->string('state');
                $table->string('country');
                $table->string('phone', 40);
                $table->string('fax', 40);
                $table->timestamps();

                //add more fields to your table here
            }
        );
    }

    /**
     * Run downgrade migration.
     */
    public function down()
    {
        $this->schema->drop('companies');
    }
}
