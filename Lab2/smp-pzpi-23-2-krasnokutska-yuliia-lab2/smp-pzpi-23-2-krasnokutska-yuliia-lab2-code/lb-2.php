#!usr/bin/php
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

function shopping(&$cart, $products){
    while (true){
        echo "№  НАЗВА                 ЦІНА\n";
        foreach ($products as $id => $item){
            printf("%-3d%-22s%3d\n", $id, $item['name'], $item['price']);
        }
        echo "   -----------\n";
        echo "0  ПОВЕРНУТИСЯ\n";
        echo "Виберіть товар: ";

        $input = read_input();

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
    echo "№  НАЗВА                 ЦІНА  КІЛЬКІСТЬ  ВАРТІСТЬ\n";
    $i = 1;
    $total_cost = 0;
    foreach ($cart as $id => $quantity) {
        $name = $products[$id]['name'];
        $price = $products[$id]['price'];
        $cost = $price * $quantity;
        printf("%-3d%-22s%5d%10d%10d\n", $i++, $name, $price, $quantity, $cost);
        $total_cost += $cost;
    }
    echo "РАЗОМ ДО CПЛАТИ: $total_cost\n";
}

function setup_profile(&$profile){
    while(true){
        $name = read_input("Ваше ім'я: ");
        if (!preg_match('/[a-zA-Zа-яА-ЯіІїЇєЄґҐ]/u', $name)) {
            echo "Імʼя повинно містити хоча б одну літеру\n";
            continue;
        }
        $profile['name'] = $name;
        break;
    }
}

function main() {
    global $cart, $products, $profile;

    while (true) {
        print_menu();
        $command = read_input();

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
