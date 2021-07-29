<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTasksTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('tasks');
        $table->addColumn('title', 'string')
            ->create();
    }
}
