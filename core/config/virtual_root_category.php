<?php
#Показывать ли виртуальную корневую категорию
#На mapsshop.ru - true, на региональных - false
$ShowVirtualRootCategory = false;

#Показывать ли специальные товары на главной вместо привязанных к главной категории
#На mapsshop.ru - true, на региональных - false
#$ShowSpecialProductsOnHomePage = false;

#Параметры виртуальной корневой категори
#При $ShowVirtualRootCategory = false - не используется
$VirtualRootCategory = array(
    'categoryID' => 1,
    'parent' => 1,
    'name' => 'Алтай',
    'UID' => '',
    'sort_order' => 10001,
);

$DomainRegion = 'ARCHANGELSK';
$DomainDescription = 'Карты Aрхангельской области';

?>