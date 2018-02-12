<?php

require 'ethereum-php-master/ethereum.php';

$rate=0.000000000000000001;

//создаём новое подключение
$ethereum = new Ethereum('192.168.56.1', 8545);

//создаём новый фильтр
$filter = new Ethereum_Filter('0x0', 'latest', '0x3b4a22858093B9942514eE42eD1B4BF177632ba3', []);

//отправляем фильтр в ноду
$result_filter=$ethereum->eth_newFilter($filter);

//получаем список events
$logs=$ethereum->eth_getFilterLogs($result_filter);

foreach ($logs as $key => $value) {

  /*
  сравниваем первый элемент масива topics, в нем хранится хэш имени события и списка типов переменных
  строка: PaymentOrder(uint256,address,uint256) тип хэштрования: Keccak-256 (для получения хэша я воспользовался онлайн сервисом)
  в остальнх элементах topics хранятся проиндексированные параметры события
  */

  if (strcasecmp($value->{'topics'}[0], "0x"."c84883193d3a69d991d82f61928c06e179b647e413da4c20be80d8c0314c2e1b") == 0) {
    echo "Payment order id:".hexdec($value->{'topics'}[1]);

    /*
    в элементе data хранятся остальные параметры события
    склеенные по 32 байта
    */

    $data=str_split(substr($value->{'data'}, 2),64);

    echo " volume:".hexdec($data[1])*$rate." ETH";

    echo "<br>";
  }

}


?>
