<?php
/**
 * Created by PhpStorm.
 * User: people2015
 * Date: 2017/1/4
 * Time: 16:32
 */
require __DIR__.'/vendor/autoload.php';

const DEBUG = false;

function get_hosts()
{
    if(DEBUG)
        return file_get_contents('hosts.txt');
    $curl = new \Curl\Curl();
    $curl->get('https://raw.githubusercontent.com/racaljk/hosts/master/hosts');
    $curl->setOpt(CURLOPT_SSL_VERIFYPEER,false);
    if ($curl->error) {
        echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        exit;
    } else {
        return $curl->response;
    }
}

function convert_host_to_rr(string  $str)
{
    $t = explode("\n", $str);
    $arr = [];
    foreach ($t as $v) {
        if ($v && is_numeric($v[0])) {
            $_t = explode("\t", $v);
            $arr[] = $_t[1] . ' IN A ' . $_t[0];
        }
    }
    return implode(PHP_EOL,$arr);
}

get_hosts();
$template = <<<HEREDOC
\$TTL 600

@ IN SOA ns01.com. root ( 2017010401 1800 600 36000 5400) 

@ IN NS ns01.com.

ns01.com. IN A 10.100.10.31

HEREDOC;




if(DEBUG){
    echo $template.convert_host_to_rr(get_hosts());
}else{
    write_to_file($template.convert_host_to_rr(get_hosts()));
}


function write_to_file($str)
{
    file_put_contents('/var/named/proxy.zone',$str);
}











//var_dump($hosts,$hosts_arr);