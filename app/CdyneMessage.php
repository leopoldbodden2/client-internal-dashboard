<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CdyneMessage extends Model
{
    use SoftDeletes;

    const API_URLS = [
        'SendMessage' => 'http://messaging.cdyne.com/Messaging.svc/SendMessage',
        'ReadMessageStatus' => 'http://messaging.cdyne.com/Messaging.svc/ReadMessageStatus',
        'ReadIncomingMessages' => 'http://messaging.cdyne.com/Messaging.svc/ReadIncomingMessages',
        'GetUnsubscribedNumbers' => 'http://messaging.cdyne.com/Messaging.svc/GetUnsubscribedNumbers',
        'CancelMessage' => 'http://sms2.cdyne.com/sms.svc/CancelMessage',
    ];

    protected $fillable = ['user_id', 'name', 'subject', 'phone', 'body', 'message_id', 'send_attempted'];

    protected $hidden = ['referenceid', 'licensekey', 'from', 'postbackurl'];

    protected $casts = [
        'attempted' => 'boolean',
        'successful' => 'boolean'
    ];
    protected $appends = [
        'licensekey',
        'from',
        'postbackurl'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getLicensekeyAttribute(){
        return $this->user->cdyne_license_key ?? false;
    }
    public function getFromAttribute(){
        return $this->user->cdyne_phone ?? false;
    }
    public function getPostbackurlAttribute(){
        return 'http://www.cdyne.com/postback.aspx'; // route('sms-receive');
    }

    public function SendMessage(){
        if($this->licensekey && $this->from){
            $messageData = [
                'UseMMS' => false,
                'Concatenate' => false,
                'IsUnicode' => false,
                'LicenseKey' => $this->licensekey,
                'Body' => $this->body,
                'From' => $this->from,
                'PostbackUrl' => $this->postbackurl,
                'ReferenceID' => $this->id,
                'Subject' => $this->subject,
                'To' => [$this->phone]
            ];

            $json=json_encode($messageData);

            $url = self::API_URLS['SendMessage'];

            $cURL = curl_init();
             
            curl_setopt($cURL,CURLOPT_URL,$url);
            curl_setopt($cURL,CURLOPT_POST,true);
            curl_setopt($cURL,CURLOPT_POSTFIELDS,$json);
            curl_setopt($cURL,CURLOPT_RETURNTRANSFER, true);  
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
            //If you desire your results in xml format, use the following line for your httpheaders and comment out the httpheaders code line above.
            //curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
            $result = curl_exec($cURL);

            curl_close($cURL);

            $array = json_decode($result, true);
            //print_r($json);
            //print_r("\n\n\n\n");

            return $array;
        }
        return false;
    }

    public function ReadMessageStatus(){
        if($this->licensekey && $this->message_id){
            $url=self::API_URLS['ReadMessageStatus']."?LicenseKey={$this->licensekey}&MessageId={$this->message_id}";
            
            $ch = curl_init(); 
            
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_HTTPGET,true);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);  
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
            
            //print_r($url);
            
            $response = curl_exec($ch); 
            curl_close($ch); 
            
            return $response;
        }
        return false;
    }

    public function ReadIncomingMessages(Carbon $start_date, Carbon $end_date, bool $unread){
        if($this->licensekey){
            $messageData = [
                'LicenseKey' => $this->licensekey,
                'StartDate' => $this->toDotnetJsonDate($start_date),
                'EndDate' => $this->toDotnetJsonDate($end_date),
                'UnreadMessagesOnly' => $unread
            ];
            $json=json_encode($messageData);

            $url = self::API_URLS['ReadIncomingMessages'];
            $cURL = curl_init();
             
            curl_setopt($cURL,CURLOPT_URL,$url);
            curl_setopt($cURL,CURLOPT_POST,true);
            curl_setopt($cURL,CURLOPT_POSTFIELDS,$json);
            curl_setopt($cURL,CURLOPT_RETURNTRANSFER, true);  
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
            //If you desire your results in xml format, use the following line for your httpheaders and comment out the httpheaders code        e above.
            //curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
            $result = curl_exec($cURL);

            curl_close($cURL);
            
            $array = json_decode($result, true);
            //print_r($json);
            //print_r("\n\n\n\n");
            
            return $array;
        }
        return false;
    }

    public function GetUnsubscribedNumbers(){
        if($this->licensekey){
            $url=self::API_URLS['GetUnsubscribedNumbers']."?LicenseKey={$this->licensekey}";
             
            $ch = curl_init(); 
            
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_HTTPGET,true);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);  
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
            
            $response = curl_exec($ch); 
            curl_close($ch); 
            
            $array = json_decode($response, true);
            
            return $array;
        }
        return false;
    }
    public function CancelMessage(){
        if($this->message_id){
            $url=self::API_URLS['CancelMessage']."MessageId={$this->message_id}";
            
            $cURL = curl_init();
            
            curl_setopt($cURL,CURLOPT_URL,$url);
            curl_setopt($cURL,CURLOPT_HTTPGET,true);
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
            
            $result = curl_exec($cURL);
            
            curl_close($cURL);

            $array = json_decode($result, true);

            return $array;
        }
        return false;
    }

    public function SetPostbackURLForLicenseKey(){
        if($this->licensekey && $this->postbackurl) {
            $url = "http://messaging.cdyne.com/Messaging.svc/SetPostbackUrlForLicenseKey?LicenseKey={$this->licensekey}&PostBackType=All&Url={$this->postbackurl}";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

            $response = curl_exec($ch);
            curl_close($ch);

            $array = json_decode($response, true);

            return $array;
        }
    }

    public function SetPostbackEnabledForLicenseKey(){
        if($this->licensekey && $this->postbackurl) {
            $url = "http://messaging.cdyne.com/Messaging.svc/GetPostbackSettingsForLicenseKey?LicenseKey={$this->licensekey}";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

            $response = curl_exec($ch);
            curl_close($ch);

            $array = json_decode($response, true);

            return $array;
        }
    }

    private function toDotnetJsonDate(Carbon $dateTime){
        return sprintf(
            '/Date(%s)/',
            $dateTime->timestamp
        );
    }
}
