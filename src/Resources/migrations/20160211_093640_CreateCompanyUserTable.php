<?php

namespace Xsanisty\UserManager\Migration;

use SilexStarter\Migration\Migration;

class CreateCompanyUserTable extends Migration
{
    /**
     * Run upgrade migration.
     */
    public function up()
    {
        $this->schema->create(
            'companies_users',
            function ($table) {
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('company_id');
                $table->boolean('primary')->default(false);
                $table->boolean('active')->default(false);
                $table->boolean('admin')->default(false);
                $table->text('permissions');
                $table->timestamps();

                //add more fields to your table here
                $table->unique(['user_id', 'company_id']);
            }
        );
    }

    /**
     * Run downgrade migration.
     */
    public function down()
    {
        $this->schema->drop('companies_users');
    }
}
