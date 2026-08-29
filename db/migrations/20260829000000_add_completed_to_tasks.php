<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCompletedToTasks extends AbstractMigration
{
    public function change(): void
    {
        $this->table('tasks')
            ->addColumn('completed', 'boolean', ['default' => false, 'null' => false])
            ->update();
    }
}
