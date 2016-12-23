<?php echo "<?php\r\n";?>

class <?php echo $name; if (!empty($parentClass)) echo " extends {$parentClass}"; echo "\r\n"; ?>
{
    public function up()
    {
        $id = $this->IblockGetIdByCode('<?php echo mb_strtolower($smart_param_1);?>');
        $this->UFCreate([
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_2);?>',
            'ENTITY_ID' => 'IBLOCK_' . $id . '_SECTION',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => '<?php echo ucfirst($smart_param_2);?>',
            ],
        ]);
    }

    public function down()
    {
        $id = $this->IblockGetIdByCode('<?php echo mb_strtolower($smart_param_1);?>');
        $entity = 'IBLOCK_' . $id . '_SECTION';
        $this->UFDelete($entity, 'UF_<?php echo mb_strtoupper($smart_param_2);?>');
    }
}
