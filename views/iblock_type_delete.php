<?php echo "<?php\r\n"; ?>

/**
 * Миграция для удаления типа инфоблока '<?php echo ucfirst($smart_param_1); ?>'.
 */
class <?php echo $name; if (!empty($parentClass)) {
    echo " extends {$parentClass}";
} echo "\r\n"; ?>
{
    public function up()
    {
        return $this->IblockTypeDelete('<?php echo mb_strtolower($smart_param_1); ?>');
    }

    public function down()
    {
        return $this->IblockTypeCreate([
            'ID' => '<?php echo mb_strtolower($smart_param_1); ?>',
            'SECTIONS' => 'Y',
            'IN_RSS' => 'N',
            'SORT' => 500,
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'LANG' => [
                'en' => [
                    'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
                    'SECTION_NAME' => '',
                    'ELEMENT_NAME' => '',
                ],
                'ru' => [
                    'NAME' => '<?php echo ucfirst($smart_param_1); ?>',
                    'SECTION_NAME' => '',
                    'ELEMENT_NAME' => '',
                ],
            ],
        ]);
    }
}
