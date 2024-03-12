<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ExampleController extends Controller
{

    public function __construct()
    {
        $this->url = "https://api.telegram.org/bot";
        $this->token = env('TOKEN_TELEGRAM_API');
        $this->chat_id = env('CHAT_ID_TELEGRAM');
    }

    //Cronjob 10p 1 lần
    public function index()
    {
        $listUser = DB::table('users')->select('email', 'password')->get()->toArray();
        if (!empty($listUser)) {
            foreach ($listUser as $item) {
                $flag = false;
                //Login api mail token
                $result = $this->getTokenMailTM($item->email, $item->password);
                $token = $result['token'] ?? '';
                $getMail = $this->getMailTM($token);
                $message_new = 'Thông báo email chưa đọc từ Email: ' . $item->email . "\n";
                if (!empty($getMail)) {
                    foreach ($getMail as $value) {
                        //True là đã xem, False là chưa xem
                        if (isset($value['seen']) && $value['seen'] == false) {
                            $flag = true;
                            $dateTime = new \DateTime($value['createdAt']);
                            $formattedDateTime = $dateTime->format('d/m/Y H:i:s');
                            $message_new .= "Subject: " . $value['subject'] . "\n" . 'CreatedAt: ' . $formattedDateTime . "\n";
                        }
                    }
                }
                if ($flag) {
                    $this->sendMessageTele($message_new, $this->chat_id);
                }
            }
        }
    }

    //CronJob 1p lần
    public function createUserMailTM()
    {
        //Lấy thông tin text từ telegram
        $telegram = $this->apiTelegram();
        if (!empty($telegram) && isset($telegram['message']['text'])) {
            $telegramText = $telegram['message']['text'];
            $parts = explode("::", $telegramText);
            if (isset($parts[0]) && $parts[0] == '/add') {
                $checkUser = DB::table('users')->where('email', '=', $parts[1])->first();
                if (!$checkUser) {
                    DB::table('users')->insert([
                        'name' => "example",
                        'email' => $parts[1],
                        'password' => $parts[2]
                    ]);
                    $message_new = "Đã thêm Email Address thành công";
                    $this->sendMessageTele($message_new, $this->chat_id);
                }
            } elseif (isset($parts[0]) && $parts[0] == '/listemail') {
                $listUser = DB::table('users')->select('email')->get()->toArray();
                if (!empty($listUser)) {
                    $message_new = 'List Email Đã Đăng Ký: ' . "\n";
                    foreach ($listUser as $value) {
                        $message_new .= $value->email . "\n";
                    }
                    $this->sendMessageTele($message_new, $this->chat_id);
                } else {
                    $message_new = "Không có Email đăng ký";
                    $this->sendMessageTele($message_new, $this->chat_id);
                }
            }
        }
    }

    public function getTokenMailTM($address, $password)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mail.tm/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"address": "' . $address . '","password": "' . $password . '"}',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        if (!empty($response)) {
            $data = json_decode($response, true);
        }
        return $data ?? [];
    }

    public function apiTelegram()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . env('TOKEN_TELEGRAM_API') . '/getUpdates',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        if (!empty($response)) {
            $response = json_decode($response, true);
            $data = end($response['result']);
        }
        return $data ?? [];
    }

    public function sendMessageTele($message_new, $chat_id)
    {
        try {
            $result = Http::get($this->url . $this->token . "/sendMessage?chat_id=" . $chat_id . "&text=" . $message_new . "&parse_mode=HTML");
            return json_decode($result->body());
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getMailTM($token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mail.tm/messages?page=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'accept: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        if (!empty($response)) {
            $response = json_decode($response, true);
        }
        return $response ?? [];
    }
}
