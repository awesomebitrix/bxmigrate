<?php

namespace marvin255\bxmigrate\checker;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use CUserTypeEntity;

class HighLoadIb implements \marvin255\bxmigrate\IMigrateChecker
{
    /**
     * @var string
     */
    protected $tableName = null;
    /**
     * @var string
     */
    protected $compiledEntity = null;

    /**
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        if (empty($tableName)) {
            throw new Exception('Table name can not be empty');
        }
        $this->tableName = $tableName;
        Loader::includeModule('highloadblock');
    }

    /**
     * @param string $migration
     * @return bool
     */
    public function isChecked($migration)
    {
        $checked = $this->getChecked();
        return isset($checked[$migration]);
    }

    /**
     * @param string $migration
     */
    public function check($migration)
    {
        $checked = $this->getChecked();
        if (!isset($checked[$migration])) {
            $hlblock = $this->infrastructureCheck();
            $class = $this->compileEntity($hlblock);
            $result = $class::add([
                'UF_MIGRATION_NAME' => $migration,
                'UF_MIGRATION_DATE' => date('d.m.Y'),
            ]);
            if (!$result->isSuccess()) {
                throw new Exception('Can\'t check migration in HL: '.implode(', ', $result->getErrorMessages()));
            }
        }
    }

    /**
     * @param string $migration
     */
    public function uncheck($migration)
    {
        $checked = $this->getChecked();
        if (isset($checked[$migration])) {
            $hlblock = $this->infrastructureCheck();
            $class = $this->compileEntity($hlblock);
            $result = $class::delete($checked[$migration]['ID']);
            if (!$result->isSuccess()) {
                throw new Exception('Can\'t delete migration in HL '.implode(', ', $result->getErrorMessages()));
            }
        }
    }

    /**
     * @return array
     */
    protected function getChecked()
    {
        $return = [];
        $hlblock = $this->infrastructureCheck();
        $class = $this->compileEntity($hlblock);
        $res = $class::getList([
            'select' => ['*'],
        ])->fetchAll();
        foreach ($res as $key => $value) {
            $return[$value['UF_MIGRATION_NAME']] = $value;
        }
        return $return;
    }

    /**
     * @param array $hlblock
     * @return string
     */
    protected function compileEntity(array $hlblock)
    {
        if ($this->compiledEntity === null) {
            global $USER_FIELD_MANAGER;
            $USER_FIELD_MANAGER->CleanCache();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $this->compiledEntity = $entity->getDataClass();
        }
        return $this->_compiledEntity;
    }

    /**
     * @return array
     */
    protected function infrastructureCheck()
    {
        $modelName = $this->getModelName();
        //проверяем существует ли таблица миграций
        $filter = array(
            'select' => array('ID', 'NAME', 'TABLE_NAME'),
            'filter' => array('=TABLE_NAME' => $this->tableName),
        );
        $hlblock = HighloadBlockTable::getList($filter)->fetch();
        //создаем таблицу, если она не существует
        if (empty($hlblock['ID'])) {
            $result = HighloadBlockTable::add([
                'NAME' => $modelName,
                'TABLE_NAME' => $this->tableName,
            ]);
            $id = $result->getId();
            if (!$id) Exception('Can\'t create HL table '.implode(', ', $result->getErrorMessages()));
        } else {
            $id = $hlblock['ID'];
        }
        //проверяем поля таблицы, чтобы были все
        $fields = [];
        $rsData = CUserTypeEntity::GetList([], [
            'ENTITY_ID' => "HLBLOCK_{$id}",
        ]);
        while ($ob = $rsData->GetNext()) {
            $fields[$ob['FIELD_NAME']] = $ob['ID'];
        }
        //название миграции
        if (empty($fields['UF_MIGRATION_NAME'])) {
            $obUserField = new CUserTypeEntity;
            $idRes = $obUserField->Add([
                'USER_TYPE_ID' => 'string',
                'ENTITY_ID' => "HLBLOCK_{$id}",
                'FIELD_NAME' => 'UF_MIGRATION_NAME',
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Название миграции',
                ]
            ]);
            if (!$idRes) throw new Exception('Can\'t create UF_MIGRATION_NAME property');
        }
        //дата миграции
        if (empty($fields['UF_MIGRATION_DATE'])) {
            $obUserField = new CUserTypeEntity;
            $idRes = $obUserField->Add([
                'USER_TYPE_ID' => 'string',
                'ENTITY_ID' => "HLBLOCK_{$id}",
                'FIELD_NAME' => 'UF_MIGRATION_DATE',
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Дата миграции',
                ]
            ]);
            if (!$idRes) throw new Exception('Can\'t create UF_MIGRATION_DATE property');
        }
        return [
            'ID' => $id,
            'NAME' => $modelName,
            'TABLE_NAME' => $this->tableName,
        ];
    }

    /**
     * @return string
     */
    protected function getModelName()
    {
        return ucfirst(str_replace(['_'], '', $this->getTableName()));
    }
}