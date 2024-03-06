<?php

function print_arr($data) : void
{
  echo "<pre>" . print_r($data, true) . "</pre>";
}

function debug($data, $log = true) : void
{
    if ($log) {
        file_put_contents(ROOT. '/logs.log', print_r($data, true), FILE_APPEND);
    } else {
        echo "<pre>" . print_r($data, true) . "</pre>";
    }
}

function send_request(string $method, array $params = []) : mixed
{
  $url = BASE_URL . $method;
  if (!empty($params)) {
    $url .= '?' . http_build_query($params);
  }

  return json_decode(file_get_contents(
    $url,
    false,
    stream_context_create(['http' => ['ignore_errors' => true]])
  ));
}

/* Estate functions */

function sendDescription($telegram, $chat_id, $row)
{

  if (isset($row['region_size'])) {
    $region_size = PHP_EOL . "Размер территории: <b>{$row['region_size']}</b>";
  } else
      $region_size = "";

  $description = <<<EOD
  <b>Информация о обьекте</b>
  Тип: <b>{$row['type']}</b>
  Город: <b>{$row['city']}</b>
  Площадь обьекта: <b>{$row['house_size']}</b> $region_size
  Цена продажи: <b>{$row['selling_price']}</b>
  Описание: <b>{$row['description']}</b>
  <a href="https://montenegrogreen.online/public/objectShare.php?id={$row['id']}">Web-версия</a>
  EOD; 

  $telegram->sendMessage([
      'chat_id' => $chat_id,
      'text'=> $description,
      'parse_mode' => 'HTML',
  ]);

}

function sendImages($telegram, $chat_id, $id)
{
  $path_to_images_html = "https://montenegrogreen.online/public/objectsImages/" . $id . '/';
  $path_to_images_php = __DIR__ . "/../public/objectsImages/" . $id . '/';
  $images = [];

  $dir_handle = @opendir($path_to_images_php) or ($telegram->sendMessage(['chat_id' => $chat_id,'text'=> "Folder opening error"]) and die());
  while ($file = readdir($dir_handle))
  {
    if($file=="." || $file == "..") continue; 
    if (filesize($path_to_images_php . $file) > 5242880) {
      $telegram->sendMessage([
          'chat_id' => BOSS_CHAT_ID,
          'text'=> "Image {$path_to_images_php}{$file} need to be compressed!",
      ]);
      continue;
    }
    array_push($images, ['type' => 'photo', 'media' => $path_to_images_html . $file]);
  }

  $images_ten_each = array_chunk($images, 10);
  foreach($images_ten_each as $images)
  {
    $telegram->sendMediaGroup([
        'chat_id' => $chat_id,
        'media' => json_encode($images),      
    ]);
  }
}

function show($query, $telegram, $chat_id, $conn)
{
  try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    if($stmt->rowCount() > 0)
    {
      foreach ($stmt as $row) {

        $id = $row['id'];

        sendDescription($telegram, $chat_id, $row);
        sendImages($telegram, $chat_id, $id);

        sleep(3);

      }
    }

    return $stmt;

  } catch (\Telegram\Bot\Exceptions\TelegramSDKException $e) {
    error_log($e->getMessage() . PHP_EOL, 3, __DIR__ . '/errors.log');
  }
}

function create_filtration_query($telegram, $chat_id)
{
  $query = "SELECT * FROM objects WHERE ";

  $fd = fopen("filter.txt", 'r') or ($telegram->sendMessage(['chat_id' => $chat_id,'text'=> "Filter.txt can't be created!"]) and die());
  while(!feof($fd))
  {
      $str = htmlentities(fgets($fd));

      if (str_contains($str,'type')) {
        $query .= "type = '" . trim(substr($str, 5)) . "' AND (";
      } else if (str_contains($str,'city')) {
        $query .= "city = '" . trim(substr($str, 5)) . "' OR ";
      } else if (str_contains($str,'selling_price')) {
        $query = substr($query,0,-3) . ") AND ";
        $query .= htmlspecialchars_decode($str);
      }

  }  
  fclose($fd);

  return $query;
}