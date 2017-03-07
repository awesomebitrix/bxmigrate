<?php echo "<?php\r\n"; ?>

/**
 * Миграция для обновления свойства '<?php echo mb_strtoupper($smart_param_2); ?>' highload инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        $id = $this->HLGetIdByCode('<?php echo ucfirst($smart_param_1); ?>');
        return $this->UFUpdate([
            'ENTITY_ID' => 'HLBLOCK_' . $id,
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_2); ?>',
        );
    }

    public function down()
    {
        $id = $this->HLGetIdByCode('<?php echo ucfirst($smart_param_1); ?>');
        return $this->UFUpdate([
            'ENTITY_ID' => 'HLBLOCK_' . $id,
            'FIELD_NAME' => 'UF_<?php echo mb_strtoupper($smart_param_2); ?>',
        );
    }
}
