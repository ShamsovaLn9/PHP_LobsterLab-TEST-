<?php
// Подключаем PrestaShop CLI
require_once(dirname(FILE).'/config/config.inc.php');
require_once(dirname(FILE).'/init.php');

// Функция для обновления товаров с использованием подготовленных запросов
function updateProductsFromCSV($csvPath) {
    // Открываем CSV файл
    if (($handle = fopen($csvPath, "r")) !== FALSE) {
        // Пропускаем заголовки
        fgetcsv($handle);
        
        // Начинаем транзакцию
        $db = Db::getInstance();
        $db->beginTransaction();
        
        try {
            // Подготавливаем запросы для обновления и добавления товаров
            $updateQuery = $db->prepare('UPDATE ' . _DB_PREFIX_ . 'product SET name = ?, price = ? WHERE id_product = ?');
            $insertQuery = $db->prepare('INSERT INTO ' . _DB_PREFIX_ . 'product (name, price) VALUES (?, ?)');
            
            // Чтение строк из CSV файла
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Валидация данных
                $id = (int)$data[0];
                $name = pSQL($data[1]);
                $price = (float)$data[2];
                
                // Проверяем, существует ли товар
                if (Product::idExists($id)) {
                    // Обновляем данные
                    $updateQuery->execute([$name, $price, $id]);
                } else {
                    // Создаем новый товар
                    $insertQuery->execute([$name, $price]);
                }
            }
            // Подтверждаем транзакцию
            $db->commit();
        } catch (Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $db->rollback();
            // Записываем ошибку в лог
            PrestaShopLogger::addLog('Ошибка при обновлении товаров: ' . $e->getMessage(), 3);
        }
        
        fclose($handle);
    }
}

// Путь к вашему CSV файлу
$csvPath = 'path_to_your_csv.csv';

// Вызываем функцию обновления товаров
updateProductsFromCSV($csvPath);
?>
