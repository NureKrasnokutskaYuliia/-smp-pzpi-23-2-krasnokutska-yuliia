k#!usr/bin/php
<?php

require_once 'products.php';

$cart = [];
$user = ['name' => '', 'age' => 0];

function print_menu(){
    echo "################################\n";
    echo "# ПРОДОВОЛЬЧИЙ МАГАЗИН \"ВЕСНА\" #\n";
    echo "################################\n";
    echo "1 Вибрати товари\n";
    echo "2 Отримати підсумковий рахунок\n";
    echo "3 Налаштувати свій профіль\n";
    echo "0 Вийти з програми\n";
    echo "Введіть команду: ";
}

function read_input($prompt = ''){
    return trim(readline($prompt));
} 

function paddings($row, $widths) {
    foreach ($row as $i => $cell) {
        echo mb_str_pad($cell, $widths[$i], ' ', STR_PAD_RIGHT) . "  ";
    }
    echo "\n";
}

function shopping(&$cart, $products){
    while (true){
        $num_len = mb_strlen("№");
        $name_len = mb_strlen("НАЗВА");
        $price_len = mb_strlen("ЦІНА");
        foreach ($products as $id => $item){
            $num_len = max(mb_strlen("$id"), $num_len);
            $name_len = max(mb_strlen($item['name']), $name_len);
            $price_len = max(mb_strlen($item['price']), $price_len);
        }

        paddings(["№", "НАЗВА", "ЦІНА"], [$num_len, $name_len, $price_len]);

        foreach ($products as $id => $item){
            paddings([$id, $item['name'], $item['price']], [$num_len, $name_len, $price_len]);
        }

        echo "   -----------\n";
        echo "0  ПОВЕРНУТИСЯ\n";

        $input = read_input("Виберіть товар: ");

        if ($input === "0") return;

        if (!array_key_exists((int)$input, $products)) {
            echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n\n";
            continue;
        }

        $id = (int)$input;
        $product = $products[$id];

        echo "Вибрано: {$product['name']}\n";

        $quantity = (int)read_input("Введіть кількість, штук: ");

        if ($quantity < 0 || $quantity >= 100){
            echo "ПОМИЛКА! Кількість повинна бути від 0 до 99\n\n";
            continue;
        }

        if ($quantity === 0){
            if (isset($cart[$id])) {
                unset($cart[$id]);
                echo "ВИДАЛЯЮ З КОШИКА\n";
            }
        } else {
            $cart[$id] = $quantity;
        }

        if (empty($cart)){
            echo "КОШИК ПОРОЖНІЙ\n\n";
        } else {
            echo "У КОШИКУ:\nНАЗВА        КІЛЬКІСТЬ\n";
            foreach ($cart as $pid => $quantity) {
                echo "{$products[$pid]['name']}  $quantity\n";
            }
            echo "\n";
        }
    }
}

function show_bill($cart, $products){
    if (empty($cart)) {
        echo "КОШИК ПОРОЖНІЙ. ДОДАЙТЕ ТОВАРИ.\n";
        return;
    }

    $num_len = mb_strlen("№");
    $name_len = mb_strlen("НАЗВА");
    $price_len = mb_strlen("ЦІНА");
    $quantity_len = mb_strlen("КІЛЬКІСТЬ");
    $cost_len = mb_strlen("ВАРТІСТЬ");

    foreach ($cart as $id => $quantity){
        $product = $products[$id];
        $price = $product['price'];
        $cost = $price * $quantity;

        $num_len = max(mb_strlen("$id"), $num_len);
        $name_len = max(mb_strlen($product['name']), $name_len);
        $price_len = max(mb_strlen("$price"), $price_len);
        $quantity_len = max(mb_strlen("$quantity"), $quantity_len);
        $cost_len = max(mb_strlen("$cost"), $cost_len);
    }

    paddings(["№", "НАЗВА", "ЦІНА", "КІЛЬКІСТЬ", "ВАРТІСТЬ"], [$num_len, $name_len, $price_len, $quantity_len, $cost_len]);

    $total_cost = 0;
    foreach ($cart as $id => $quantity){
        $product = $products[$id];
        $price = $product['price'];
        $cost = $price * $quantity;
        $total_cost += $cost;
        
        paddings([$id, $product['name'], $price, $quantity, $cost], [$num_len, $name_len, $price_len, $quantity_len, $cost_len]);
    }

    echo str_repeat("-", $num_len + $name_len + $price_len + $quantity_len + $cost_len + 10) . "\n";
    echo "РАЗОМ ДО СПЛАТИ: $total_cost грн\n";
}

function setup_profile(&$profile){
    while(true){
        $name = read_input("Ваше ім'я: ");
        if (!preg_match('/[a-zA-Zа-яА-ЯіІїЇєЄґҐ]/u', $name)) {
            echo "Імʼя повинно містити хоча б одну літеру\n";
            continue;
        }

        $age = read_input("Ваш вік: ");
        if (!is_numeric($age) || (int)$age < 7 || (int)$age > 150) {
            echo "Вік користувача повинен бути від 7 до 150 років.\n";
            continue;
        }

        $profile['name'] = $name;
        $profile['age'] = (int)$age; 
        break;
    }
}

function main() {
    global $cart, $products, $user;

    while (true) {
        print_menu();
        $command = read_input();

        if (!in_array($command, ["0", "1", "2", "3"])) {
            echo "ПОМИЛКА! Введіть правильну команду\n\n";
            continue;
        }

        switch ($command) {
            case "1":
                shopping($cart, $products);
                break;
            case "2":
                show_bill($cart, $products);
                break;
            case "3":
                setup_profile($profile);
                break;
            case "0":
                echo "Дякуємо за покупки! До побачення.\n";
                exit;
            default:
                echo "ПОМИЛКА! Введіть правильну команду\n";
                break;
        }
        echo "\n";
    }
} 

main()
?>
