<?php

namespace ant\db;

class Migration extends \yii\db\Migration {
	const INDEX_FK = 'fk';
	const INDEX_UNIQUE = 'uq';
	const INDEX = 'ix';
	
	const FK_TYPE_CASCADE = 'CASCADE';
	const FK_TYPE_RESTRICT = 'RESTRICT';
	const FK_TYPE_SET_NULL = 'SET NULL';
	
	protected $tableOptions = [
		'mysql' => [
			'string' => 'CHARACTER SET {characterSet} COLLATE {collate} ENGINE={engine}',
			'params' => [
				'characterSet' => 'utf8',
				'collate' => 'utf8_unicode_ci',
				'engine' => 'InnoDB',
			],
		],
	];
	
	protected function id() {
		return $this->primaryKey()->unsigned();
	}
	
	protected function autoId() {
		return $this->primaryKey()->unsigned();
	}
	
	protected function foreignId($nullable = true) {
		$col = $this->integer()->unsigned();
		if ($nullable) $col->null()->defaultValue(null);
		
		return $col;
	}
	
	protected function morphId($nullable = true) {
		return $this->foreignId($nullable);
	}
	
	protected function morphClass($nullable = true) {
		return $this->foreignId($nullable);
	}
	
	protected function nullableMorphClass() {
		return $this->morphClass();
	}
	
	protected function nullableMorphId() {
		return $this->morphId();
	}
	
	protected function createIndexFor($columns) {
		$this->createIndex($this->getIndexName($this->tableName, $columns, self::INDEX), $this->tableName, $columns, false);
	}
	
	protected function createUniqueIndexFor($columns) {
		$this->createIndex($this->getIndexName($this->tableName, $columns, self::INDEX_UNIQUE), $this->tableName, $columns, true);
	}
	
	protected function addForeignKeyTo($toTable, $relationColumn, $delete = null, $update = null) {
		$fromTable = $this->tableName;
		$fromForeignKey = is_array($relationColumn) ? key($relationColumn) : $relationColumn;
		$toPrimaryKey = is_array($relationColumn) ? current($relationColumn) : 'id';
		
		$this->addForeignKey($this->getIndexName($this->tableName, $fromForeignKey, self::INDEX_FK),  $fromTable,  $fromForeignKey,  $toTable, $toPrimaryKey, $delete, $update);
	}
	
	protected function dropForeignKeyTo($toTable, $relationColumn) {
		$fromTable = $this->tableName;
		$fromForeignKey = is_array($relationColumn) ? key($relationColumn) : $relationColumn;
		
		$this->dropForeignKey($this->getIndexName($this->tableName, $fromForeignKey, self::INDEX_FK),  $fromTable);
	}
	
	protected function getIndexName($table, $columns, $type) {
		$columns = (array) $columns;
		return str_replace(['{', '}', '%'], '', $table).'_'.implode('_', $columns).'_'.$type;
	}
	
	// Refer: http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
	protected function getTableOptions($options = []) {
		$driverName = $this->db->driverName;
		$tableOptions = \yii\helpers\ArrayHelper::merge($this->tableOptions[$driverName]['params'], $options);
		$optionString = $this->tableOptions[$driverName]['string'];
		
		$placeholders = [];
		foreach ((array) $tableOptions as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }
		
		return strtr($optionString, $placeholders);
	}
	
	protected function alterZeroDateTimeColumn($table, $columns) {
		$sql = [];
		$existingColumns = \Yii::$app->db->schema->getTableSchema($table, true)->columns;
		
		foreach ($columns as $column) {
			if (is_array($column)) {
				
			} else {
				if (isset($existingColumns[$column])) {
					$columnName = $column;
					$sql[] = 'CHANGE `'.$columnName.'` `'.$columnName.'` timestamp NULL DEFAULT NULL';
				};
			}
		}
		$this->execute('
			ALTER TABLE '.$table.' '.implode(', ', $sql).';
		');
		foreach ($columns as $column) {
			if (isset($existingColumns[$column])) {
				$columnName = is_array($column) ? '' : $column;
				$this->update($table, [$columnName => null], '`'.$columnName.'` < "1971-01-01 00:00:01"');
			}
		}
	}
}