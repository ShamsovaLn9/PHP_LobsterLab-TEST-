<?php
// Подключаем PrestaShop CLI
require_once(dirname(FILE).'/config/config.inc.php');
require_once(dirname(FILE).'/init.php');

// Функция для обновления товаров
function updateProductsFromCSV($csvPath) {
    // Открываем CSV файл
    if (($handle = fopen($csvPath, "r")) !== FALSE) {
        // Пропускаем заголовки
        fgetcsv($handle);
        
        // Чтение строк из CSV файла
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Проверяем, существует ли товар
            $product = new Product((int)$data[0]);
            if (Validate::isLoadedObject($product)) {
                // Обновляем данные
                $product->name = $data[1];
                $product->price = $data[2];
                $product->update();
            } else {
                // Создаем новый товар
                $product = new Product();
                $product->id = $data[0];
                $product->name = $data[1];
                $product->price = $data[2];
                $product->add();
            }
        }
        fclose($handle);
    }
}

// Путь к вашему CSV файлу
$csvPath = 'path_to_your_csv.csv';

// Вызываем функцию обновления товаров
updateProductsFromCSV($csvPath);
?>
